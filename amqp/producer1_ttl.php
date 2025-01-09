<?php

use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

require_once 'Connect.php';

$conn = new Connect();

$exchange   = 'test.exchange.aaattl';
$queue      = 'test.queue.aaattl';
$routingKey = 'test.route_key.aaattl';

// 声明初始化交换器
$conn->channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$table = new AMQPTable();
$table->set('x-message-ttl', 10000);

// 声明初始化队列
$conn->channel->queue_declare($queue, false, true, false, false, false, $table);

// 将队列与某个交换器进行绑定
$conn->channel->queue_bind($queue, $exchange, $routingKey);


while (true) {
    // 生成消息
    $body = json_encode(['msg' => time()]);
    $message = new AMQPMessage($body, [
        'content_type'  => 'text/plain',
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
    ]);

    // 推送消息到某个交换器
    $result = $conn->channel->basic_publish($message, $exchange, $routingKey);
    echo "Send...\r\n";
    usleep(50);
}

