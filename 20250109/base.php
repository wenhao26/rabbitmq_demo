<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

$connection = new AMQPStreamConnection('localhost', 5672, 'admin', 'admin');
$channel    = $connection->channel();
