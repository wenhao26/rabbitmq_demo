<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

ini_set('display_errors','On');
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

class RabbitMQ
{

    private $host = '192.168.7.63';
    private $port = 5672;
    private $user = 'hujiao';
    private $password = '13579abc';
    protected $connection;
    protected $channel;


    /**
     * RabbitMQ constructor.
     */
    public function __construct()
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->password);
        $this->channel    = $this->connection->channel();
    }

    /**
     * 生成信息
     * @param $message
     */
    public function sendMessage($message, $routeKey, $exchange = '', $properties = [])
    {
        $data = new AMQPMessage(
            $message, $properties
        );
        $this->channel->basic_publish($data, $exchange, $routeKey);
    }

    /**
     * 消费消息
     * @param $queueName
     * @param $callback
     * @throws \ErrorException
     */
    public function consumeMessage($queueName,$callback)
    {
        $this->channel->basic_consume($queueName, '', false, false, false, false, $callback);
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}

/**
 * 使用RabbitMQ实现延时队列功能
 * Class DelayQueue
 * @package RabbitMQ
 */
class DelayQueue extends RabbitMQ
{

    /**
     * 创建延时队列
     * @param $ttl
     * @param $delayExName
     * @param $delayQueueName
     * @param $queueName
     */
    public function createQueue($ttl, $delayExName, $delayQueueName, $queueName)
    {
        $args = new AMQPTable([
            'x-dead-letter-exchange'    => $delayExName,
            'x-message-ttl'             => $ttl, //消息存活时间，单位毫秒
            'x-dead-letter-routing-key' => $queueName
        ]);
        $this->channel->queue_declare($queueName, false, true, false, false, false, $args);
        //绑定死信queue
        $this->channel->exchange_declare($delayExName, AMQPExchangeType::DIRECT, false, true, false);
        $this->channel->queue_declare($delayQueueName, false, true, false, false);
        $this->channel->queue_bind($delayQueueName, $delayExName, $queueName, false);
    }
}

// 生产者

$delay = new DelayQueue();

$ttl            = 1000 * 100;//订单100s后超时
$delayExName    = 'delay-order-exchange';//超时exchange
$delayQueueName = 'delay-order-queue';//超时queue
$queueName      = 'ttl-order-queue';//订单queue

$delay->createQueue($ttl, $delayExName, $delayQueueName, $queueName);

//100个订单信息，每个订单超时时间都是10s
for ($i = 0; $i < 100; $i++) {
    $data = [
        'order_id' => $i + 1,
        'remark'   => 'this is a order test'
    ];
    $delay->sendMessage(json_encode($data), $queueName);
    echo 'Message: ' . $i . PHP_EOL;
    sleep(1);
}

