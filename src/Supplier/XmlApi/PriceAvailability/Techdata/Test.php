<?php

const EOL = PHP_EOL;

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\Techdata\Client($config['techdata']);

$request = $client->createRequest();
#$request->addPartnum('TD-1892ZJ');
$request->addPartnum('TD-0331ZE');
#pr($request->toXml());
$response = $client->sendRequest($request);
$items = $response->getItems();
pr($items);


// Response-1
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/Techdata/fixtures/td-pna-response-2.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\Techdata\Response($xml);
#$x = $response->parseXml();
#pr($x);
