<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Ingram\Client;
use Supplier\Ingram\PriceAvailabilityRequest;
use Supplier\Ingram\PriceAvailabilityResponse;
use Supplier\Ingram\PurchaseOrderRequest;
use Supplier\Ingram\PurchaseOrderResponse;
use Supplier\Ingram\OrderNumberMapper;

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
    $result = $client->getPriceAvailability($sku);

    pr($result->getFirst()->toArray());
}

function getOrder()
{
    return [ // this comes from ca_order_notes
        'id' => '2754',
        'channel' => 'Amazon-ACA',
        'date' => '2016-08-29',
        'orderId' => '702-6000945-2557809',
        'mgnOrderId' => '',
        'express' => '0',
        'buyer' => 'Sam Wang',
        'address' => '123 Esna Park',
        'city' => 'Toronto',
        'province' => 'ON',
        'province' => 'Ontario',
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
        'comment' => 'TEST PO ONLY - DO NOT SHIP',
        'customerPO' => '', //'TEST PO ONLY - DO NOT SHIP',
    ];
}

function testPurchaseOrderRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $order = getOrder();

    $request = new Supplier\Ingram\PurchaseOrderRequest();
    $request->setConfig($config[ConfigKey::INGRAM]);
    $request->addOrder($order);

    $xml = $request->toXml();

    echo $xml;
}

function testPurchaseOrderResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-po-response-1.xml');
/*
$xml=<<<EOS
<?xml version="1.0" encoding="ISO-8859-1"?>
<ERROR Number="20001">Invalid Inbound XML Document</ERROR>
EOS;
*/
    $response = new Supplier\Ingram\PurchaseOrderResponse($xml);
    $result = $response->parseXml();

    pr($result);
}

function realPurchaseOrder()
{
    /*
      When placing test orders, please follow these steps to ensure that your orders are not
      released:

      - Advise your Ingram Sales representative in advance of placing any test orders.
      - Place your orders on an Ecommerce Customer HOLD, by indicating ‘H’ as the
        Autorelease value (Note: Dtype Order Requests don’t contain the Autorelease element,
        however, these orders will trigger another HOLD in our system automatically.)
      - Specify the CustomerPO as “TEST PO ONLY – DO NOT SHIP”
      - Indicate within order on a Comment Line that the order is only a test. For example
      - <CommentText>TEST PO ONLY – DO NOT SHIP</CommentText>
      - Communicate any test orders to your IM Sales Representative, so that these are not
        shipped and can be voided.
    */

    // Testing SKUs for Canadian Partners
    $sku = 'ING-21592L'; // This sku will have 0 stock available in any of the ALCs
    $sku = 'ING-21593L'; // This sku is stocked and is in stock.
    $sku = 'ING-21594L'; // This SKU has a product class code of “X”(Directship SKUs).
                         // SKUs with this class code can only be ordered via the
                         // Dtype Order Request xml transaction.
                         // Licensing and warranty products are examples of class X SKUs.
#   $sku = 'ING-69905Z';

    #$config = include __DIR__ . '/app/config/xmlapi.php';
    $config = include __DIR__ . '/app/config/config.php';  // !!
    $config['xmlapi']['ingram']['autoRelease'] = 'H';

    $order = getOrder();
    $order['sku'] = $sku;

    $client = new Client($config);
    $result = $client->purchaseOrder($order);

    pr($result);
}

function testOrderNumberMapper()
{
#   echo strlen('1609090319559852'), PHP_EOL;
#   echo strlen('70260009452557809'), PHP_EOL;
    echo OrderNumberMapper::getFakeOrderNo('702-6000945-2557809'), PHP_EOL;
    echo OrderNumberMapper::getRealOrderNo('70260009452557809'), PHP_EOL;
    echo OrderNumberMapper::getFakeOrderNo('702-6000945-2557809000'), PHP_EOL;
    echo OrderNumberMapper::getFakeOrderNo('ING-'.date('Ymd-Hi')), PHP_EOL;
}

function testOrderTrackingRequest()
{
    $config = include __DIR__ . '/app/config/xmlapi.php';

    $request = new Supplier\Ingram\OrderTrackingRequest();
    $request->setConfig($config[ConfigKey::INGRAM]);
    $request->setOrder('702-9287700-2279402');

    $xml = $request->toXml();

    echo $xml;
}

function testOrderTrackingResponse()
{
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-orderTracking-Response-1.xml');
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-orderTracking-error-1.xml');
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-orderTracking-error-2.xml');
    $xml = file_get_contents(__DIR__ . './src/Supplier/Ingram/fixtures/ing-orderTracking-Response-2.xml');

    $response = new Supplier\Ingram\OrderTrackingResponse($xml);
    $result = $response->parseXml();

    pr($xml);
    pr($result);
}

function realOrderTracking()
{
    $config = include __DIR__ . '/app/config/config.php';  // !!

    $client = new Client($config);
    $result = $client->getOrderStatus('701-4610082-2957050');

    pr($result);
}

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
#realPurchaseOrder();
#testOrderNumberMapper();

#testOrderTrackingRequest();
#testOrderTrackingResponse();
#realOrderTracking();
