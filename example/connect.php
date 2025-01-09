<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

/*$config = [
    'host'     => '1.14.177.49',
    'vhost'    => '/',
    'port'     => 5672,
    'login'    => 'test',
    'password' => 'test@#8888'
];*/
/* $config = [
    'host' => '192.168.7.114',
    'port' => 5672,
    'login' => 'jackmx',
    'password' => 'kkY3n4d2w4Apj',
    'vhost' => '/',
]; */
$config = [
    'host' => '192.168.7.63',
    'vhost' => '/',
    'port' => 5672,
    'login' => 'hujiao',
    'password' => '13579abc'
];

// 连接broker
$connect = new AMQPConnection($config);
if (!$connect->connect()) {
    echo 'Can not connect to the broker';
    exit();
}
