<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

/* $config = [
    'host' => 'localhost',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'admin',
    'password' => 'admin'
]; 
$config = [
    'host' => '1.14.177.49',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'test',
    'password' => 'test@#8888'
];*/
$config = [
    'host' => '192.168.23.128',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'hujiao',
    'password' => '13579abc'
];

// 连接broker
$cnn = new AMQPConnection($config);
if (!$cnn->connect()) {
    echo 'Can not connect to the broker';
    exit();
}

// 在连接内创建一个通道
$ch = new AMQPChannel($cnn);
// 创建一个交换机
$ex = new AMQPExchange($ch);
// 声明路由键，一定要和消费者端一致
$routingKey = 'key_1';
// 声明交换机名称，一定要和消费者端一致
$exchangeName = 'exchange_1';
// 设置交换机名称
$ex->setName($exchangeName);
// 设置交换机类型
$ex->setType(AMQP_EX_TYPE_DIRECT);
// 设置交换机持久
$ex->setFlags(AMQP_DURABLE);
// 声明交换机
$ex->declareExchange();

// 创建10个消息
/*for ($i = 1; $i <= 10; $i++) {
    // 消息内容
    $msg = [
        'data' => 'message_' . $i,
        'hello' => 'php-rabbitMQ'
    ];

    // 发送消息到交换机，并返回发送结果
    // delivery_mode:2声明消息持久，持久的队列+持久的消息在RabbitMQ重启后才不会丢失
    echo 'Send Message:' . $ex->publish(json_encode($msg), $routingKey, AMQP_NOPARAM, ['delivery_mode' => 2]) . "\n";
}*/

while (true) {
    // 消息内容
    $msg = [
        'data' => 'message_' . mt_rand(1000, 99999),
        'hello' => 'php-rabbitMQ'
    ];

    // 发送消息到交换机，并返回发送结果
    // delivery_mode:2声明消息持久，持久的队列+持久的消息在RabbitMQ重启后才不会丢失
    echo 'Send Message:' . $ex->publish(json_encode($msg), $routingKey, AMQP_NOPARAM, ['delivery_mode' => 2]) . "\n";
}