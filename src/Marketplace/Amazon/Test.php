<?php

include './public/init.php';

//$config = include './app/config/amazon.php';

$client = new Marketplace\Amazon\Client('bte-amazon-ca');
$client->getOrders();
