<?php

include './public/init.php';

//$config = include './app/config/amazon.php';

$client = new Marketplace\Amazon\Client('bte-amazon-ca');
$client->setChannel('Amazon-ACA');
$client->getOrders();

$client = new Marketplace\Amazon\Client('bte-amazon-us');
$client->setChannel('Amazon-US');
$client->getOrders();
