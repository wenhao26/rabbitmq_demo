<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

Class Connect {
    public $client;
    public $channel;

    public function __construct()
    {
        $this->client = new AMQPStreamConnection('1.14.177.49', 5672, 'admin', '13579abc');
        $this->channel = $this->client->channel();
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->channel->close();
        $this->client->close();
    } 
}