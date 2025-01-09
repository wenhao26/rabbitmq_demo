<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

$config = [
    'host'     => '192.168.7.63',
    'vhost'    => '/',
    'port'     => 5672,
    'login'    => 'hujiao',
    'password' => '13579abc'
];

// 连接broker
$conn = new AMQPConnection($config);
try {
    if (!$conn->connect()) {
        echo '连接失败';
        exit();
    }
} catch (AMQPConnectionException $e) {
    echo $e->getCode() . ':' . $e->getMessage();
    exit();
}

function halt($data) {
    echo '<pre>';
    var_dump($data);
    die;
}