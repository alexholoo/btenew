<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\DH\Client;
use Supplier\DH\PriceAvailabilityRequest;
use Supplier\DH\PriceAvailabilityResponse;
use Supplier\DH\PurchaseOrderRequest;
use Supplier\DH\PurchaseOrderResponse;

function testPriceAvailabilityRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\DH\PriceAvailabilityRequest();
    $request->setConfig($config[ConfigKey::DH]);
    $request->addPartnum('DH-HLL2360DWCA');
   #$request->addPartnum('DH-00WG660CA');
   #$request->addPartnum('DH-00WG685CA');
   #$request->addPartnum('DH-DEFNANOSBKW');

    $xml = $request->toXml();

    echo $xml;
}

function testPriceAvailabilityResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/DH/fixtures/dh-pna-response-1');
    $response = new Supplier\DH\PriceAvailabilityResponse($xml);
    $result = $response->parseXml();

    #pr($result->getItems());
    pr($result->getFirst()->toArray());
}

function realPriceAvailability()
{
    $sku = 'DH-00WG660CA';
    $sku = 'DH-00WG685CA';
    $sku = 'DH-DEFNANOSBKW';
    $sku = 'DH-HLL2360DWCA';

    #$config = include __DIR__ . '/app/config/xmlapi.php'; // ??
    $config = include __DIR__ . '/app/config/config.php';  // ??

    $client = new Client($config);
    $result = $client->getPriceAvailability($sku);

    pr($result->getFirst()->toArray());
}

function getOrder()
{
    return [ // this comes from ca_order_notes
        'id' => '2754',
        'channel' => 'Amazon-ACA',
        'date' => '2016-08-29',
        'orderId' => '701-3707503-5766613',
        'mgnOrderId' => '',
        'express' => '0',
        'buyer' => 'Sam Wang',
        'address' => '123 Esna Park',
        'city' => 'Toronto',
        'province' => 'British Columbia',
        'postalcode' => 'R3B 0J7',
        'country' => 'CA',
        'phone' => '800-900-1020',
        'email' => 'samwang@email.com',
        'sku' => 'DH-VG248QECA',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '',
        'comment' => 'TEST PO ONLY & DO NOT SHIP',
    ];
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new Supplier\DH\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::DH]);
    $request->setOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/DH/fixtures/dh-order-entry-response-2');
    $response = new Supplier\DH\PurchaseOrderResponse($xml);
    $result = $response->parseXml();

    pr($xml);
    pr($result);
}

function realPurchaseOrder()
{
    $config = include __DIR__ . '/app/config/config.php';

    // Test Account
    $config['xmlapi']['dh']['username'] = '800712TSTXML';
    $config['xmlapi']['dh']['password'] = 'BTE@dh2015';

    $order = getOrder();

    $client = new Client($config);
    $result = $client->purchaseOrder($order);

    pr($result);
}

function testOrderStatusRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\DH\OrderStatusRequest();
    $request->setConfig($config[ConfigKey::DH]);
    $request->setOrder('701-3707503-5766613');

    $xml = $request->toXml();

    echo $xml;
}

function testOrderStatusResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/DH/fixtures/DH-OS-Response-2');
    $response = new Supplier\DH\OrderStatusResponse($xml);
    $result = $response->parseXml();

    pr($xml);
    pr($result);
}

function realOrderStatus()
{
    $config = include __DIR__ . '/app/config/config.php';

    $client = new Client($config);
#   $result = $client->getOrderStatus('702-9287700-2279402'); // french
    $result = $client->getOrderStatus('702-0611280-7687416');

    pr($result);
}

function realGetTracking()
{
    $config = include __DIR__ . '/app/config/config.php';

    $client = new Client($config);
    $client->getTracking();
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
#realPurchaseOrder();

#testOrderStatusRequest();
#testOrderStatusResponse();
#realOrderStatus();

#realGetTracking();
