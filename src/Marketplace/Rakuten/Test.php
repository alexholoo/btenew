<?php

include './public/init.php';

$config = include APP_DIR . '/config/rakuten.php';

$client = new Marketplace\Rakuten\Client('CA'); // not avail
$client = new Marketplace\Rakuten\Client('US');

$client->getOrders();
