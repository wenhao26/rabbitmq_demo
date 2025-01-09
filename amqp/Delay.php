<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

require_once __DIR__ . '/vendor/autoload.php';

class Delay
{
    private $host = '1.14.177.49';
    private $port = 5672;
    private $user = 'admin';
    private $psss = '13579abc';

    private $msg;
    private $channel;
    private $connection;

    //  过期时间
    const TIMEOUT_5_S  = 5;     // 5s
    const TIMEOUT_10_S = 10;    // 10s

    private $exchange_logs    = "logs";
    private $exchange_direct  = "direct";
    private $exchange_delayed = "delayed";

    private $queue_delayed = "delayedQueue";

    const EXCHANGETYPE_FANOUT  = "fanout";
    const EXCHANGETYPE_DIRECT  = "direct";
    const EXCHANGETYPE_DELAYED = "x-delayed-message";

    public function __construct($type = false)
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->psss);
        $this->channel    = $this->connection->channel();
        // 声明Exchange
        $this->channel->exchange_declare($this->exchange_delayed, self::EXCHANGETYPE_DIRECT, false, true, false, false, false, new AMQPTable(["x-delayed-type" => self::EXCHANGETYPE_DIRECT]));
        $this->channel->queue_declare($this->queue_delayed, false, true, false, false);
        $this->channel->queue_bind($this->queue_delayed, $this->exchange_delayed, $this->queue_delayed);
    }

    /**
     * delay creat message
     */
    public function createMessageDelay($msg, $time)
    {
        $delayConfig = [
            'delivery_mode'       => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'application_headers' => new AMQPTable(['x-delay' => $time * 1000])
        ];
        $msg         = new AMQPMessage($msg, $delayConfig);
        return $msg;
    }

    /**
     * delay send message
     */
    public function sendDelay($msg, $time = self::TIMEOUT_10_S)
    {
        $msg = $this->createMessageDelay($msg, $time);
        $this->channel->basic_publish($msg, $this->exchange_delayed, $this->queue_delayed);
        $this->channel->close();
        $this->connection->close();
    }

    /**
     * delay consum
     */
    public function consumerDelay()
    {
        $callback = function ($msg) {
            echo ' [x] ', $msg->body, "\n";
            $this->channel->basic_ack($msg->delivery_info['delivery_tag'], false);
        };
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queue_delayed, '', false, false, false, false, $callback);
        echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        $this->channel->close();
        $this->connection->close();
    }
}