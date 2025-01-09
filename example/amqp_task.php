<?php
include_once 'connect.php';

// 创建channel
$channel = new AMQPChannel($connect);

// 创建exchange交换机
// 声明交换机名称，一定要和消费者端一致
$exName = 'vm_test_exchange_1';
$ex = new AMQPExchange($channel);
$ex->setName($exName);
$ex->setType(AMQP_EX_TYPE_DIRECT); // direct 完成匹配模型
$ex->setFlags(AMQP_DURABLE);
$ex->declareExchange();

// 声明路由键，一定要和消费者端一致
$routingKey = 'vm_test_key_1';

// 队列名称
$queueName = 'vm_test_queue_1';
$q = new AMQPQueue($channel);
$q->setName($queueName);
$q->setFlags(AMQP_DURABLE);
echo 'queue status:' . $q->declareQueue() . "\n";
echo 'queue bind:' . $q->bind($exName, $routingKey) . "\n"; // 将消息通过制定的路由发送

while (true) {
    $date = date('Ymd H:i:s');
    $message = json_encode(['date' => $date, 'id' => generateRequestId()], JSON_UNESCAPED_UNICODE);
    $ex->publish($message, $routingKey);
    echo $date . "：Send Message...\n";
    //usleep(100);
}

//$channel->commitTransaction();
$connect->disconnect();

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
