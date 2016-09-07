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
    $response = $client->getPriceAvailability($sku);

    $result = $response->parseXml();

    pr($result->getFirst()->toArray());
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = [ // this comes from ca_order_notes
        'id' => '2754',
        'channel' => 'Amazon-ACA',
        'date' => '2016-08-29',
        'orderId' => '701-3707503-5766610',
        'mgnOrderId' => '',
        'express' => '0',
        'buyer' => 'Sam Wang',
        'address' => '123 Esna Park',
        'city' => 'Toronto',
        'province' => 'ON',
        'postalcode' => 'R3B 0J7',
        'country' => 'CA',
        'phone' => '800-900-1020',
        'email' => 'samwang@email.com',
        'sku' => 'DH-5530287',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '',
        'comment' => 'Please ship ASAP!',
    ];

    $request = new Supplier\DH\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::DH]);
    $request->addOrder($order);

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
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
 realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
 realPurchaseOrder();
