<?php
use PhpAmqpLib\Exchange\AMQPExchangeType;

require_once 'Connect.php';

$conn = new Connect();

$exchange   = 'test.exchange.aaa';
$queue      = 'test.queue.aaa';
$routingKey = 'test.route_key.aaa';

/*// 声明初始化交换器
$conn->channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

// 声明初始化队列
$conn->channel->queue_declare($queue, false, true, false, false);

// 将队列与某个交换器进行绑定
$conn->channel->queue_bind($queue, $exchange, $routingKey);*/

$callback = function($msg) {
    $str = "[x] Received {$msg->body} \r\n";
    echo $str;
};

$conn->channel->basic_consume($queue, '', false, true, false, false, $callback);

while(count($conn->channel->callbacks)) {
    $conn->channel->wait();
}