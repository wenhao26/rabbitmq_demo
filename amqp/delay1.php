<?php
require_once 'Delay.php';

$mq = new Delay();
$mq->sendDelay("fasdfasdfadsf", 5);