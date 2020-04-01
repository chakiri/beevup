<?php


namespace App\Websocket;


use App\Entity\User;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Symfony\Component\Security\Core\Security;

class TopicHandler implements WampServerInterface
{

    protected $clients;

    protected $subscribed = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
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
                if ($key == $entryData['subject']){
                    $user->broadcast($entryData);
                //Send it also to proper user
                }elseif($key == $entryData['from']){
                    $user->broadcast($entryData);
                }
            }
        }else{
            // If the lookup topic object isn't set there is no one to publish to
            if (!array_key_exists($entryData['subject'], $this->subscribed)) {
                return;
            }
            $topic = $this->subscribed[$entryData['subject']];

            // re-send the data to all the clients subscribed to that category
            $topic->broadcast($entryData);

            //Send notification to all topics
            foreach ($this->subscribed as $otherTopic){
                if ($otherTopic !== $topic){
                    $notifData = [
                        'topicFrom' => $entryData['subject'],
                        'type' => 'notification'
                    ];
                    $otherTopic->broadcast($notifData);
                }
            }
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