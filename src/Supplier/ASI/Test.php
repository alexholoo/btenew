<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Supplier;
use Supplier\ASI\Client;
use Supplier\ASI\PriceAvailabilityRequest;
use Supplier\ASI\PriceAvailabilityResponse;
use Supplier\ASI\PurchaseOrderRequest;
use Supplier\ASI\PurchaseOrderResponse;
use Supplier\Model\Order;

function testPriceAvailabilityRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\ASI\PriceAvailabilityRequest();
    $request->setConfig($config[ConfigKey::ASI]);
    $request->addPartnum('AS-177420');
    $request->addPartnum('AS-172600');

    $xml = $request->toXml();

    echo $xml, PHP_EOL;
}

function testPriceAvailabilityResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/ASI/fixtures/asi-pna-response-1.xml');
    $response = new Supplier\ASI\PriceAvailabilityResponse($xml);
    $result = $response->parseXml();

    pr($result->getFirst()->toArray());
}

function realPriceAvailability()
{
    $sku = 'AS-172600';
    $sku = 'AS-177420';

    #$config = include __DIR__ . '/app/config/xmlapi.php'; // ??
    $config = include __DIR__ . '/app/config/config.php';  // ??

    $client = new Client($config);
    #$client = Supplier::createClient($sku);

    $result = $client->getPriceAvailability($sku);

    pr($result->getFirst()->toArray());
}

function getOrder()
{
    return new Order([ // this comes from ca_order_notes
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
        'sku' => 'AS-VG248QECA',
        'price' => '87.39',
        'qty' => '1',
        'shipping' => '0.00',
        'mgnInvoiceId' => 'n/a',
        // extra info from user
        'branch' => '2216', // Toronto, 3316=>Vancouver
        'comment' => 'TEST PO ONLY & DO NOT SHIP',
    ]);
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::ASI]);
    $request->setOrder($order);

    $xml = $request->toXml();

    echo $xml, PHP_EOL;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/ASI/fixtures/asi-po-response-1.xml');
    $xml = file_get_contents(__DIR__ . './src/Supplier/ASI/fixtures/asi-po-response-2.xml');
    $response = new PurchaseOrderResponse($xml);
    $result = $response->parseXml();

    pr($xml);
    pr($result);
}

function realPurchaseOrder()
{
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
 testPurchaseOrderResponse();
#realPurchaseOrder();
