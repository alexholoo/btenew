<?php

require __DIR__ . '/public/init.php';

function pr($d) { print_r($d); echo EOL; }

$config = include __DIR__ . '/app/config/xmlapi.php';
$client = new \Supplier\XmlApi\PriceAvailability\DH\Client($config['dh']);

#or

#use Supplier\XmlApi\PriceAvailability\Factory;
#$factory = new \Supplier\XmlApi\PriceAvailability\Factory($config);
#$client = $factory->createClient('dh');

$request = $client->createRequest();
 $request->addPartnum('DH-HLL2360DWCA');
#$request->addPartnum('DH-00WG660CA');
#$request->addPartnum('DH-00WG685CA');
#$request->addPartnum('DH-DEFNANOSBKW');

$response = $client->sendRequest($request);
pr($response->getXmlDoc());
$items = $response->getItems();
pr($items);
