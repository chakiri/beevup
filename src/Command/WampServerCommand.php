<?php
namespace App\Command;

use App\Websocket\TopicHandler;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;
use React;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WampServerCommand extends Command
{
    protected static $defaultName = "run:wamp-server";

    private $pusher;

    //Use pusher as service to can inject other services into it
    public function __construct($name = null, TopicHandler $topicHandler)
    {
        parent::__construct($name);
        $this->pusher = $topicHandler;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $port = $_ENV['WAMP_PORT'];
        $output->writeln("Starting wamp server on port " . $port);
        $loop   = Factory::create();
        //$pusher = new TopicHandler();
        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new Context($loop);
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', array($this->pusher, 'onMessage'));
        // Set up our WebSocket server for clients wanting real-time updates
        if ($_ENV['APP_ENV'] === "dev"){
            $webSock = new Server('0.0.0.0:' . $port, $loop); // Binding to 0.0.0.0 means remotes can connect
        }else{
            //Implemente SSL in ratchet to handle https
            $webSock = new React\Socket\SecureServer(
                new Server('0.0.0.0:' . $port, $loop),
                $loop,
                array(
                    'local_cert' => '/etc/letsencrypt/live/beevup.fr/cert.pem',
                    'local_pk' => '/etc/letsencrypt/live/beevup.fr/privkey.pem',
                    'verify_peer' => false,
                    'verify_peer_name' => false
                )
            );
        }

        $webServer = new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $this->pusher
                    )
                )
            ),
            $webSock
        );
        $loop->run();
    }
}