<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

require_once 'Connect.php';

$conn = new Connect();

$exchange   = 'test.exchange.aaadelay';
$queue      = 'test.queue.aaadelay';
$routingKey = 'test.route_key.aaadelay';

// 声明初始化交换器
$conn->channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

// 声明初始化队列
$conn->channel->queue_declare($queue, false, true, false, false, false);

// 将队列与某个交换器进行绑定
$conn->channel->queue_bind($queue, $exchange, $routingKey);

$callback = function ($msg) use ($conn) {
    echo ' [x] ', $msg->body, "\n";
    $conn->channel->basic_ack($msg->delivery_info['delivery_tag'], false);
};
$conn->channel->basic_qos(null, 1, null);
$conn->channel->basic_consume($queue, '', false, false, false, false, $callback);
while (count($conn->channel->callbacks)) {
    $conn->channel->wait();
}
/*while ($conn->channel->is_consuming()) {
    $conn->channel->wait();
    usleep(80);
}*/


