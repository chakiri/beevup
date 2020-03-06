<?php

namespace App\Websocket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class MessageHandler implements MessageComponentInterface
{
    //All connections join the server
    protected $connections;

    //All connections as users  => user[connection, user, channels]
    protected $users = [];

    protected $botName = "ChatBot";

    protected $defaultChannel = "general";

    public function __construct()
    {
        $this->connections = new SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->connections->attach($conn);

        // $this->users[$conn->resourceId] is the current connection on connections array

        //Add connection to users
        $this->users[$conn->resourceId] = [
            'connection' => $conn,
            'user' => '',
            'channels' => []
        ];
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);
        unset($this->users[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->connections->detach($conn);
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $messageData = json_decode($msg);

        var_dump($from);

        if ($messageData === null) return false;

        // Initialize message data
        $action = $messageData->action ?? 'unknown';
        $channel = $messageData->channel ?? $this->defaultChannel;
        $user = $messageData->user ?? $this->botName;
        $message = $messageData->message ?? '';

        //Check action
        switch ($action){
            case 'subscribe':
                $this->subscribeToChannel($from, $channel, $user);
                return true;
            case 'unsubscribe':
                $this->unsubscribeFromChannel($from, $channel, $user);
                return true;
            case 'messageToChannel':
                $this->sendMessageToChannel($from, $channel, $user, $message);
                return true;
            case 'messageToUser':
                $this->sendMessageToUser($from, $channel, $user, $message);
                return true;
            default:
                echo sprintf('This action "%s" is not supported yet', $action);
                break;
        }
        return false;
    }

    /**
     * Subscribe connection to a giver channel
     *
     * @param $conn
     * @param $channel
     * @param $user
     */
    private function subscribeToChannel(ConnectionInterface $conn, $channel, $user){
        //Add channel to user connection channels
        $this->users[$conn->resourceId]['channels'][$channel] = $channel;
        $this->sendMessageToChannel($conn, $channel, $this->botName, $user." joined #".$channel);
    }

    /**
     * Unsubscribe connection to a given channel
     *
     * @param ConnectionInterface $conn
     * @param $channel
     * @param $user
     */
    private function unsubscribeFromChannel(ConnectionInterface $conn, $channel, $user){
        if (array_key_exists($channel, $this->users[$conn->resourceId]['channels'])){
            unset($this->users[$conn->resourceId]['channels']);
        }
        $this->sendMessageToChannel($conn, $channel, $this->botName, $user." left #".$channel);
    }

    /**
     * Send message to all connections in given channel
     *
     * @param ConnectionInterface $conn
     * @param $channel
     * @param $user
     * @return bool
     */
    private function sendMessageToChannel(ConnectionInterface $conn, $channel, $user, $message){
        if (!$this->users[$conn->resourceId]['channels'][$channel]) return false;

        //var_dump(sprintf($this->users[$conn->resourceId]['channels'][$channel]));
        var_dump($user);

        foreach ($this->users as $connectionId => $userConnection){
            if (array_key_exists($channel, $userConnection['channels'])){
                $userConnection['connection']->send(json_encode([
                    'action' => 'message',
                    'channel' => $channel,
                    'user' => $user,
                    'message' => $message
                ]));
            }

        }
        return true;
    }
}