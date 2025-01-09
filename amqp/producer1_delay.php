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
$conn->channel->exchange_declare(
    $exchange,
    AMQPExchangeType::DIRECT,
    false,
    true,
    false,
    false,
    false,
    new AMQPTable([
        'x-delayed-type' => AMQPExchangeType::DIRECT
    ])
);

// 声明初始化队列
$conn->channel->queue_declare($queue, false, true, false, false, false);

// 将队列与某个交换器进行绑定
$conn->channel->queue_bind($queue, $exchange, $routingKey);

$t   = random_int(3000, 6000);
$msg = "mes time=" . time() . ",delay sec={$t}\n\r";

$delayConfig = [
    'delivery_mode'       => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    'application_headers' => new AMQPTable([
        'x-delay' => 5000
    ])
];
$msg = new AMQPMessage($msg, $delayConfig);
$conn->channel->basic_publish($msg, $exchange, $routingKey);


