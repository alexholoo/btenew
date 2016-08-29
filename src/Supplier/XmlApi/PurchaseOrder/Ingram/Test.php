<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Ingram/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Ingram/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Ingram/Response.php';

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PurchaseOrder\Ingram\Client($config['ingram']);

$request = $client->createRequest();

$order = [
    'orderNo' => '11223344',
    'endUserPoNumber' => '',
    'comment' => 'Please ship ASAP!',
    'address' => '123 Esna Park',
    'city' => 'Toronto',
    'state' => 'ON',
    'zipcode' => 'M2H 3A4',
    'country' => 'Canada',
    'contact' => 'Sam Wang',
    'phone' => '800-900-1020',
    'email' => 'samwang@email.com',
    'sku' => 'ING-357869',
    'price' => '32.98',
    'qty' => '1',
    'branch' => 'Toronto',
];

$request->addOrder($order);

#pr($request->toXml());


$xml = file_get_contents(__DIR__ . './src/Supplier/XmlApi/PurchaseOrder/Ingram/fixtures/ing-po-response-1.xml');
$response = new \Supplier\XmlApi\PurchaseOrder\Ingram\Response($xml);
//pr($response);
#$x = $response->parseXml();
pr($response->getOrders());
pr($response->getStatus());
pr($response->getErrorMessage());
