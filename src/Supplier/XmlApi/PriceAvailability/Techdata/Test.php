<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Techdata/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Techdata/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Techdata/Response.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { var_export($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\Techdata\Client($config['techdata']);

$request = $client->createRequest();
$request->addPartnum('TD-00WG660CA');
#$request->addPartnum('TD-00WG685CA');
pr($request->toXml());

#$response = $client->sendRequest($request);
#$items = $response->getItems();
#pr($items);


// Response-1
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Techdata/fixtures/td-pna-response-1.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\Techdata\Response($xml);
#$x = $response->parseXml();
#pr($x);
