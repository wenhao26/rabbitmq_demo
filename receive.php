<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin');
$channel = $connection->channel();
$channel->queue_declare('Email', false, false, false, false);

echo "[*] Waiting for messages. To exit press CTRL+C', \r\n";

$callback = function($msg) {
    $str = "[x] Received {$msg->body} \r\n";
	echo $str;
};

$channel->basic_consume('Email', '', false, true, false, false, $callback);

while(count($channel->callbacks)) {
    $channel->wait();
}