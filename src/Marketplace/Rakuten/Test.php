<?php

include './public/init.php';

function testClient()
{
    $config = include APP_DIR . '/config/rakuten.php';

    $client = new Marketplace\Rakuten\Client('CA'); // not avail
    $client = new Marketplace\Rakuten\Client('US');

    $client->getOrders();
}
#testClient();

function testMasterOrderList()
{
    $m = new Marketplace\Rakuten\MasterOrderList('US');
    $m->generate();
}
testMasterOrderList();
