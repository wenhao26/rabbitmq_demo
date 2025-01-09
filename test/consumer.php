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

// 队列
// 创建消息队列
$q = new AMQPQueue($ch);
// 设置队列名称
$q->setName($queueName);
// 设置队列持久类型
$q->setFlags(AMQP_DURABLE); // 持久化到磁盘
// 声明消息队列
$q->declareQueue();

// 路由键
// 绑定路由
$q->bind($ex->getName(), $routingKey);

// 接收消息并进行处理回调方法
$q->consume('receive', AMQP_AUTOACK);
$connect->disconnect();


// 监听
function receive($envelope, $queue) {
    $msg = $envelope->getBody();
    echo $msg . "\n";
}


















