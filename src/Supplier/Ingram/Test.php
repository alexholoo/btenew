<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Ingram\Client;
use Supplier\Ingram\PriceAvailabilityRequest;
use Supplier\Ingram\PriceAvailabilityResponse;
use Supplier\Ingram\PurchaseOrderRequest;
use Supplier\Ingram\PurchaseOrderResponse;

function testPriceAvailabilityRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\Ingram\PriceAvailabilityRequest();
    $request->setConfig($config[ConfigKey::INGRAM]);
   #$request->addPartnum('ING-36438W');
    $request->addPartnum('ING-69905Z');

    $xml = $request->toXml();

    echo $xml;
}

function testPriceAvailabilityResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-pna-response-2.xml');
    $response = new Supplier\Ingram\PriceAvailabilityResponse($xml);
    $result = $response->parseXml();

    pr($result->getFirst()->toArray());
}

function realPriceAvailability()
{
    $sku = 'ING-36438W';
    $sku = 'ING-69905Z';

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
        'sku' => 'ING-357869',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '',
        'comment' => 'Please ship ASAP!',
    ];

    $request = new Supplier\Ingram\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::INGRAM]);
    $request->addOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-po-response-1.xml');
    $response = new Supplier\Ingram\PurchaseOrderResponse($xml);
    $result = $response->parseXml();

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
