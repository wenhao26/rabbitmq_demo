<?php
require_once 'Delay.php';

$mq = new Delay('x-delayed-message');
$mq->consumerDelay();