<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/Response.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/Warehouse.php';
require __DIR__ . '/public/trace.php';

require __DIR__ . '/public/init.php';

use Supplier\XmlApi\Synnex\Warehouse;

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\Synnex\Client($config['synnex']);

$request = $client->createRequest();
$request->addPartnum('SYN-11223344');
#$request->addPartnum('SYN-22334455');

//pr($request->toXml());


// Response-1
$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/fixtures/price-response-1.xml');
$response = new \Supplier\XmlApi\PriceAvailability\Synnex\Response($xml);
#$x = $response->parseXml();
$x = $response->getItems();
#pr($x);


// Response-2
$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Synnex/fixtures/price-response-2.xml');
$response = new \Supplier\XmlApi\PriceAvailability\Synnex\Response($xml);
$x = $response->getItems();
pr($x);

