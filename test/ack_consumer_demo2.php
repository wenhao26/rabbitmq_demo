<?php
require_once 'connect.php';

$routingKey   = 'test_key_1'; // 声明路由键
$exchangeName = 'test_exchange_1'; // 声明交换机名称
$queueName    = 'test_queue_1'; // 声明队列名称

// == 消息确认机制 ==

// 创建通道
$ch = new AMQPChannel($conn);

// 创建交换机
$ex = new AMQPExchange($ch);

// 交换机
// 设置交换机名称
$ex->setName($exchangeName);
// 设置交换机类型
$ex->setType(AMQP_EX_TYPE_DIRECT);
// 设置交换机持久类型。1=不持久化到磁盘，宕机数据消息；2=持久化到磁盘
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

// 接收消息并处理回调
$q->consume('receive');

// 处理的回调方法
function receive($envelop, $queue) {
    /*echo $envelop->getBody() . "\n";
    $queue->ack($envelop->getDeliveryTag(), AMQP_NOPARAM);*/

    // 重回队列
    $body = $envelop->getBody();
    echo $body . "\n";
    if ($body == 11111) {
        $queue->ack($envelop->getDeliveryTag(), AMQP_NOPARAM);
    } else {
        echo "将消息打回，重回队列\n";
        $queue->nack(true, AMQP_NOPARAM);
    }

}



