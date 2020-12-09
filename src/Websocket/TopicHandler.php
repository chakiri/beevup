<?php


namespace App\Websocket;

use App\Repository\MessageNotificationRepository;
use App\Repository\TopicRepository;
use App\Service\Chat\SaveMessage;
use App\Service\SaveNotification;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class TopicHandler implements WampServerInterface
{

    protected $clients;

    protected $connections = [];

    protected $subscribed = [];

    protected $saveNotification;

    protected $topicHandler;

    protected $topicRepository;

    protected $messageNotificationRepository;

    protected $saveMessage;

    protected $manager;

    public function __construct(EntityManagerInterface $manager, SaveNotification $saveNotification, \App\Service\TopicHandler $topicHandler, TopicRepository $topicRepository, MessageNotificationRepository $messageNotificationRepository, SaveMessage $saveMessage)
    {
        $this->clients = new \SplObjectStorage;
        $this->saveNotification = $saveNotification;
        $this->topicHandler = $topicHandler;
        $this->topicRepository = $topicRepository;
        $this->messageNotificationRepository = $messageNotificationRepository;
        $this->saveMessage = $saveMessage;
        $this->manager = $manager;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onSubscribe(ConnectionInterface $conn, $subject)
    {
        echo "Subsribed : {$subject->getId()} \n";
        //Push current connection by it's id as key
        $this->subscribed[$subject->getId()] = $subject;
        $this->connections[$subject->getId()] = $conn->resourceId;
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onMessage($entry)
    {
        //Check mysql connection
        //$this->getMysqlConnection();

        //Reconnect to Doctrine anyway
        $this->manager->getConnection()->close();
        $this->manager->getConnection()->connect();

        $entryData = json_decode($entry, true);

        //If it's private chat
        if ($entryData['isprivate'] == true){
            foreach ($this->subscribed as $key => $user) {
                //If reciever is connected
                //Send it also to proper user
                if ($key == $entryData['subject'] || $key == $entryData['from']){
                    $user->broadcast($entryData);
                }
            }
            //Save notif if user not connected
            if (!array_key_exists($entryData['subject'], $this->subscribed)){
                $this->saveNotification->save($entryData['subject'], $entryData['from']);
            }
        }else{
            // If the lookup topic object isn't set there is no one to publish to
            if (!array_key_exists($entryData['subject'], $this->subscribed)) {
                return;
            }
            //$topic = $this->subscribed[$entryData['subject']];
            // re-send the data to all the clients subscribed to that topic
            //$topic->broadcast($entryData);

            // !! Send only to users who have this topic !! //
            $users = $this->topicHandler->getUsersByTopic($entryData['subject']);

            //Send notification to all connected user who have topic
            foreach ($this->subscribed as $key => $user){
                //Send only to user not topics
                if (/*$key != $entryData['subject'] && */is_int($key) == true){
                    foreach ($users as $userHasTopic){
                        if ($key == $userHasTopic->getId())
                            $user->broadcast($entryData);
                    }
                }
            }

            //If user not connected to channel send notification
            foreach ($users as $user){
                if (!array_key_exists($user->getId(), $this->subscribed)){
                    $this->saveNotification->save($user->getId(), $entryData['subject']);
                }
            }
        }

        //Save message in Database
        $this->saveMessage->save($entryData['from'], $entryData['message'], $entryData['isprivate'], $entryData['subject']);

    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        //Unset connexion user from list
        foreach ($this->connections as $key => $connection){
            if ($connection == $conn->resourceId){
                if (is_int($key)){
                    unset($this->subscribed[$key]);
                    unset($this->connections[$key]);
                    echo "Closed : " . $key . "\n";
                }
            }
        }
    }

    private function getMysqlConnection(): void
    {
        //Check if mysql is always connected
        if (!$this->manager->getConnection()->isConnected()){
            $this->manager->getConnection()->close();
            $this->manager->getConnection()->connect();
            echo "mysql reconnected\n";
        }else{
            echo "mysql already connected\n";
        }
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        echo "Unsubscribing to $topic\n";
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        echo "Publishing to $topic\n";
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        echo "Calling $topic\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}