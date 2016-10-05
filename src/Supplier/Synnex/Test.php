<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Synnex\Client;
use Supplier\Synnex\PriceAvailabilityRequest;
use Supplier\Synnex\PriceAvailabilityResponse;
use Supplier\Synnex\PurchaseOrderRequest;
use Supplier\Synnex\PurchaseOrderResponse;

function testPriceAvailabilityRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\Synnex\PriceAvailabilityRequest();
    $request->setConfig($config[ConfigKey::SYNNEX]);
    $request->addPartnum('SYN-5590245');
   #$request->addPartnum('SYN-5471137');
   #$request->addPartnum('SYN-5497540');

    $xml = $request->toXml();

    echo $xml;
}

function testPriceAvailabilityResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Synnex/fixtures/synnex-pna-response-1.xml');
    $response = new Supplier\Synnex\PriceAvailabilityResponse($xml);
    $result = $response->parseXml();

    pr($result->getFirst()->toArray());
}

function realPriceAvailability()
{
    $sku = 'SYN-5590245';
    $sku = 'SYN-5497540';
    $sku = 'SYN-5471137';

    #$config = include __DIR__ . '/app/config/xmlapi.php'; // ??
    $config = include __DIR__ . '/app/config/config.php';  // ??

    $client = new Client($config);
    $result = $client->getPriceAvailability($sku);

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
        'postalcode' => 'M9W 5Z9',
        'country' => 'CA',
        'phone' => '800-900-1020',
        'email' => 'samwang@email.com',
        'sku' => 'SYN-5637038',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '57',
        'comment' => 'Price match D&H $335.55',
    ];
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new Supplier\Synnex\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::SYNNEX]);
    $request->addOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Synnex/fixtures/synnex-po-response-6.xml');
    $response = new Supplier\Synnex\PurchaseOrderResponse($xml);
    $result = $response->parseXml();

    pr($xml);
    pr($result);
}

function realPurchaseOrder()
{
    #$config = include __DIR__ . '/app/config/xmlapi.php';
    $config = include __DIR__ . '/app/config/config.php';  // !!

    $order = getOrder();

    $client = new Client($config);
    $result = $client->purchaseOrder($order);

    pr($result);
}

function testFreightQuoteRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new Supplier\Synnex\FreightQuoteRequest();
    $request->setConfig($config[ConfigKey::SYNNEX]);
    $request->addOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testFreightQuoteResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Synnex/fixtures/synnex-fq-response-3.xml');
    $response = new Supplier\Synnex\FreightQuoteResponse($xml);
    $result = $response->parseXml();

    //pr($xml);
    //pr($result);
    pr($result->toHtml('ship_method'));
}

function realFreightQuote()
{
    #$config = include __DIR__ . '/app/config/xmlapi.php';
    $config = include './app/config/config.php';  // !!

    $order = getOrder();

    $client = new Client($config);
    $result = $client->getFreightQuote($order);

    pr($result);
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
#realPurchaseOrder();

#testFreightQuoteRequest();
#testFreightQuoteResponse();
#realFreightQuote();
