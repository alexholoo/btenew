<?php

include './public/init.php';

function testClient()
{
    $config = include APP_DIR . '/config/newegg.php';
   #$client = new Marketplace\Newegg\Client('US'); // n/a
    $client = new Marketplace\Newegg\Client('CA');
    $client->getOrders();
}

#testClient();

function testMasterOrderList()
{
    $m = new Marketplace\Newegg\MasterOrderList();
    $m->generate();
    $m->generateAddress();
}

#testMasterOrderList();
