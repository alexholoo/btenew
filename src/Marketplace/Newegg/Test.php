<?php

include './public/init.php';

$config = include APP_DIR . '/config/newegg.php';
//$client = new Marketplace\Newegg\Client('US'); // n/a
$client = new Marketplace\Newegg\Client('CA');
$client->getOrders();
