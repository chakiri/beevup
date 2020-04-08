<?php


namespace App\Websocket;

use App\Service\SaveNotification;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Symfony\Component\Security\Core\Security;

class TopicHandler implements WampServerInterface
{

    protected $clients;

    protected $subscribed = [];

    protected $saveNotification;

    public function __construct(SaveNotification $saveNotification)
    {
        $this->clients = new \SplObjectStorage;
        $this->saveNotification = $saveNotification;
    }


    public function onSubscribe(ConnectionInterface $conn, $subject)
    {
        echo "Subsribed : {$subject->getId()} \n";
        //Push current connection by it's id as key
        $this->subscribed[$subject->getId()] = $subject;
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onMessage($entry) {
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
            if (array_key_exists($entryData['subject'], $this->subscribed) == false){
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

            //Send notification to all connected user
            foreach ($this->subscribed as $key => $user){
                //Send only to user not topics
                if (/*$key != $entryData['subject'] && */is_int($key) == true){
                    $user->broadcast($entryData);
                }
            }

            // !! Send only to users who have this topic !! //
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

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Closed\n";
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