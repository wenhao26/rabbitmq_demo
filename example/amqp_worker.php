<?php
include_once 'connect.php';

$exName = 'exchange_3';
$queueName = 'queue_3';
$routingKey = 'routing_key_3';

$channel = new AMQPChannel($connect);

/*
$ex = new AMQPExchange($channel);
$ex->setName($ex_name);
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
echo "Exchange status:".$ex->declare()."\n";
*/

// 创建队列 queue
$qu = new AMQPQueue($channel);
$qu->setName($queueName);
$qu->setFlags(AMQP_DURABLE);
//echo 'queue status:' . $q->declareQueue() . "\n";
// 绑定交换机和队列
$qu->bind($exName,$routingKey);

//阻塞模式接收消息
echo "Message:\n";
try {
    $qu->consume('processMessage', AMQP_AUTOACK);
} catch (AMQPChannelException $e) {
} catch (AMQPConnectionException $e) {
} catch (AMQPEnvelopeException $e) {
} //自动ACK应答
/*$msg = $qu->get(AMQP_AUTOACK);
if($msg){
    var_dump($msg->getBody());
}*/

$connect->disconnect();

//消息回调函数 processMessage
function processMessage($envelope, $queue) {
    //var_dump($envelope->);
    $msg = $envelope->getBody();
    echo $msg."\n";
}