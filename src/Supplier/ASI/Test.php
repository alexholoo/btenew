<?php

require __DIR__ . '/public/init.php';

use Supplier\ConfigKey;
use Supplier\Factory;
use Supplier\ASI\Client;
use Supplier\ASI\PriceAvailabilityRequest;
use Supplier\ASI\PriceAvailabilityResponse;

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
    #$client = Factory::createClient($sku, 'PA');

    $result = $client->getPriceAvailability($sku);

    pr($result->getFirst()->toArray());
}

function testPurchaseOrderRequest() { }
function testPurchaseOrderResponse() { }
function realPurchaseOrder() { }

#testPriceAvailabilityRequest();
#testPriceAvailabilityResponse();
#realPriceAvailability();

#testPurchaseOrderRequest();
#testPurchaseOrderResponse();
#realPurchaseOrder();
