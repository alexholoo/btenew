<?php

const EOL = PHP_EOL;

require __DIR__ . '/src/Supplier/XmlApi/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Techdata/Client.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Techdata/Request.php';
require __DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Techdata/Response.php';

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PurchaseOrder\Techdata\Client($config['techdata']);

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
    'sku' => 'TD-357869',
    'price' => '32.98',
    'qty' => '1',
    'branch' => 'Toronto',
];

$request->addOrder($order);

pr($request->toXml());


$xml = file_get_contents(__DIR__ . './src/Supplier/XmlApi/PurchaseOrder/Techdata/fixtures/td-po-response-1.xml');
$response = new \Supplier\XmlApi\PurchaseOrder\Techdata\Response($xml);
//pr($response);
#$x = $response->parseXml();
#pr($response);
