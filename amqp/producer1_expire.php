<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

require_once 'Connect.php';

$conn = new Connect();

$exchange   = 'test.exchange.aaaexpire';
$queue      = 'test.queue.aaaexpire';
$routingKey = 'test.route_key.aaaexpire';

// 声明初始化交换器
$conn->channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

// 声明初始化队列
/*$table = new AMQPTable();
$table->set('x-expires', 10000);
$conn->channel->queue_declare($queue, false, true, false, false, false, $table);*/
$conn->channel->queue_declare($queue, false, true, false, false, false);

// 将队列与某个交换器进行绑定
$conn->channel->queue_bind($queue, $exchange, $routingKey);

while (true) {
    $t = random_int(10000, 100000);

    // 生成消息
    $body = json_encode(['msg' => time()]);
    $message = new AMQPMessage($body, [
        'content_type'  => 'text/plain',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'expiration'    => "{$t}"
    ]);

    // 推送消息到某个交换器
    $result = $conn->channel->basic_publish($message, $exchange, $routingKey);
    echo "Send... {$t} \r\n";
    usleep(250);
}

