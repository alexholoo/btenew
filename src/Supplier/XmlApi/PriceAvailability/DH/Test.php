<?php

const EOL = PHP_EOL;

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PriceAvailability\DH\Client($config['dh']);

$request = $client->createRequest();
 $request->addPartnum('DH-HLL2360DWCA');
#$request->addPartnum('DH-00WG660CA');
#$request->addPartnum('DH-00WG685CA');
#$request->addPartnum('DH-DEFNANOSBKW');

$response = $client->sendRequest($request);
pr($response);
$items = $response->getItems();
pr($items);
