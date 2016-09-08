<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Techdata\Client;
use Supplier\Techdata\PriceAvailabilityRequest;
use Supplier\Techdata\PriceAvailabilityResponse;
use Supplier\Techdata\PurchaseOrderRequest;
use Supplier\Techdata\PurchaseOrderResponse;

function testPriceAvailabilityRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\Techdata\PriceAvailabilityRequest();
    $request->setConfig($config[ConfigKey::TECHDATA]);
   #$request->addPartnum('TD-1892ZJ');
    $request->addPartnum('TD-0331ZE');

    $xml = $request->toXml();

    echo $xml;
}

function testPriceAvailabilityResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Techdata/fixtures/td-pna-response-3.xml');
    $response = new Supplier\Techdata\PriceAvailabilityResponse($xml);
    $result = $response->parseXml();

    pr($result);
   #pr($result->getFirst()->toArray());
}

function realPriceAvailability()
{
    $sku = 'TD-0331ZE';
    $sku = 'TD-1892ZJ';

    #$config = include __DIR__ . '/app/config/xmlapi.php'; // ??
    $config = include __DIR__ . '/app/config/config.php';  // ??

    $client = new Client($config);
    $response = $client->getPriceAvailability($sku);

    $result = $response->parseXml();

    pr($result->getFirst());
}

function getOrder()
{
    return [ // this comes from ca_order_notes
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
        'sku' => 'TD-357869',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '',
        'comment' => 'TEST PO ONLY - DO NOT SHIP',
    ];
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new Supplier\Techdata\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::TECHDATA]);
    $request->addOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Techdata/fixtures/td-po-response-2.xml');
    $response = new Supplier\Techdata\PurchaseOrderResponse($xml);
    $result = $response->parseXml();

    pr($response);
    pr($result);
}

function realPurchaseOrder()
{
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
#realPurchaseOrder();
