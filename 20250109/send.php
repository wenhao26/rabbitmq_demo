<?php

use PhpAmqpLib\Message\AMQPMessage;

require_once 'base.php';

$queueStatus = $channel->queue_declare('queue_20250109', false, false, false, false);
var_dump($queueStatus);

$msg = new AMQPMessage('message_' . time());
$channel->basic_publish($msg, '', 'queue_20250109');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();
