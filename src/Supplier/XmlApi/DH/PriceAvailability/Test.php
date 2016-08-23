<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/DH/PriceAvailability/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/DH/PriceAvailability/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/DH/PriceAvailability/Response.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { var_export($d); echo EOL; }

$client = new \Supplier\XmlApi\DH\PriceAvailability\Client($config['dh']);

$request = $client->createRequest();
$request->addPartnum('DH-00WG660CA');
$request->addPartnum('DH-00WG685CA');

$response = $client->sendRequest($request);
$items = $response->getItems();
pr($items);
