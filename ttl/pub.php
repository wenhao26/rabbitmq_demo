<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

ini_set('display_errors','On');
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

$host = '1.14.177.49';
$port = 5672;
$user = 'test';
$password = 'YybbRijD';

$connection = new AMQPStreamConnection($host, $port, $user, $password);
$channel = $connection->channel();

$channel->exchange_declare(
    'delay-10s-exchange',
    AMQPExchangeType::DIRECT,
    false,
    true,
    false
);

$table = new AMQPTable(['x-message-ttl' => 1000 * 10]);
$channel->queue_declare('delay-10s-queue', false, true, false, false, false, $table);
$channel->queue_bind('delay-10s-queue', 'delay-10s-exchange', 'delay-10s-key');

$head = array_merge([
    'content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT],
    ['expiration' => '6000']
);
$msg = new AMQPMessage('Test TTL 10s', $head);
$res = $channel->basic_publish($msg, 'delay-10s-exchange', 'delay-10s-key');

echo '<pre>';
var_dump($res);