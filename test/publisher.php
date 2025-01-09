<?php
require_once 'connect.php';

// 创建通道
$ch = new AMQPChannel($conn);

// 创建交换机
$ex = new AMQPExchange($ch);

$routingKey   = 'test_key_1'; // 声明路由键
$exchangeName = 'test_exchange_1'; // 声明交换机名称
$queueName    = 'test_queue_1'; // 声明队列名称

// 交换机
// 设置交换机名称
$ex->setName($exchangeName);
// 设置交换机类型
$ex->setType(AMQP_EX_TYPE_DIRECT);
// 设置交换机持久类型
$ex->setFlags(AMQP_DURABLE); // 持久化到磁盘
// 声明交换机
$ex->declareExchange();

while (true) {
    $date = date('Ymd H:i:s');
    usleep(80);
    $message = json_encode(['date' => $date, 'id' => generateRequestId()], JSON_UNESCAPED_UNICODE);
//    $ex->publish($message, $routingKey);
    $ex->publish($message, $routingKey, AMQP_NOPARAM, ['delivery_mode' => 2]);
    echo $date . "：Send Message...\n";
}

function generateRequestId() {
    $uniqueStr = uniqid(mt_rand(), true);
    $charId    = strtoupper(md5($uniqueStr));
    $hyphen    = chr(45); // "-"

    return substr($charId, 0, 8) . $hyphen
        . substr($charId, 8, 4) . $hyphen
        . substr($charId, 12, 4) . $hyphen
        . substr($charId, 16, 4) . $hyphen
        . substr($charId, 20, 12);
}

