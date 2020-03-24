<?php


namespace App\Websocket;


use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class TopicHandler implements WampServerInterface
{

    protected $clients;

    protected $subscribedTopics = [];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }


    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        if (!array_key_exists($topic->getId(), $this->subscribedTopics)){
            $this->subscribedTopics[$topic->getId()] = $topic;
            echo "Subscribing to $topic\n";
        }

        $this->clients->attach($conn);
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onMessage($entry) {
        $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['topic'], $this->subscribedTopics)) {
            return;
        }
        $topic = $this->subscribedTopics[$entryData['topic']];

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);

        //Send notification to all topics
        foreach ($this->subscribedTopics as $otherTopic){
            if ($otherTopic !== $topic){
                $notifData = [
                    'topicFrom' => $entryData['topic'],
                    'type' => 'notification'
                ];
                $otherTopic->broadcast($notifData);
            }
        }

    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        echo "Unsubscribing to $topic\n";
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        //$topic->broadcast($event);
        echo "Publishing to $topic\n";
        /*foreach ($this->clients as $client) {
            if ($conn !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send('notification');
            }
        }*/
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