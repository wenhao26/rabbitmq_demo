<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('Email', false, false, false, false);

while (true) {
    $email = randStr() . '@qq.com';
	$msg = new AMQPMessage(date('Y-m-d H:i:s') . ' ' . $email);
	$channel->basic_publish($msg, '', 'Email');

	echo "[x] " . date('Y-m-d H:i:s') . "：Sent '{$email}'\n";
	sleep(1);
}

$channel->close();
$connection->close();
// var_dump($connection);

function randStr($len = 6, $format = 'default') {
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            break;
    }
    mt_srand((double)microtime() * 1000000 * getmypid());
    $password = '';
    while (strlen($password) < $len)
        $password .= substr($chars, (mt_rand() % strlen($chars)), 1);

    return $password;
}