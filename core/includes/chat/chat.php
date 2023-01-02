<?php
// TODO: PHP Ratchet for Sockets - Setup Sockets, Port Config, Start and End Sockets Server
// TODO: Chat Manager Backend
// TODO: Chat Frontend
// TODO: Chat Automated Responses
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// Make sure composer dependencies have been installed
require '../../external/vendor/autoload.php';

// Load Classes
require '../../core.php';

/**
 * Class for Live Chat
 */
class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from != $client) {
                $client->send($msg);
            }
        }
        $e = Encrypt::initiate();
        $e->enc('test');
        //
        echo $msg;
        //print_r( $this->clients );
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    public function firebase_livechat_frontend(): void {

    }

    public function firebase_livechat_backend(): void {

    }

}