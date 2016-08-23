<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/DH/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/DH/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/PriceAvailability/DH/Response.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { var_export($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\DH\Client($config['dh']);

$request = $client->createRequest();
$request->addPartnum('DH-00WG660CA');
#$request->addPartnum('DH-00WG685CA');

$response = $client->sendRequest($request);
$items = $response->getItems();
pr($items);
