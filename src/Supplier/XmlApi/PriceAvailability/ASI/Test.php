<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

#use Supplier\XmlApi\PriceAvailability\Factory;
#$factory = new \Supplier\XmlApi\PriceAvailability\Factory($config);
#$client = $factory->createClient('dh');

$client = new \Supplier\XmlApi\PriceAvailability\ASI\Client($config['asi']);

$request = $client->createRequest();
$request->addPartnum('AS-177420');
$request->addPartnum('AS-172600');

$response = $client->sendRequest($request);
pr($response->getXmlDoc());
$items = $response->getItems();
pr($items);

// fixtures
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PriceAvailability/ASI/fixtures/asi-pna-response-2.xml');
#$response = new \Supplier\XmlApi\PriceAvailability\ASI\Response($xml);
#pr($response->getItems());
