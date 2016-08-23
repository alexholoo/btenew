<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/Synnex/PriceAvailability/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/Synnex/PriceAvailability/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/Synnex/PriceAvailability/Response.php';
require __DIR__ . '/src/Supplier/XmlApi/Synnex/Warehouse.php';
require __DIR__ . '/public/trace.php';

use Supplier\XmlApi\Synnex\Warehouse;

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\Synnex\PriceAvailability\Client($config['synnex']);

$request = $client->createRequest();
$request->addPartnum('11223344');
$request->addPartnum('22334455');

//pr($request->toXml());


// Response-1
$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/Synnex/PriceAvailability/fixtures/price-response-1.xml');
$response = new \Supplier\XmlApi\Synnex\PriceAvailability\Response($xml);
$x = $response->parseXml();
#pr($x);


// Response-2
$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/Synnex/PriceAvailability/fixtures/price-response-2.xml');
$response = new \Supplier\XmlApi\Synnex\PriceAvailability\Response($xml);
$x = $response->parseXml();
pr($x);

