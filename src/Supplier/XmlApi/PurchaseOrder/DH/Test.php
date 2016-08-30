<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

$client = new \Supplier\XmlApi\PurchaseOrder\DH\Client($config['dh']);

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
    'sku' => 'DH-357869',
    'price' => '32.98',
    'qty' => '1',
    'branch' => 'Toronto',
];

#$request->addOrder($order);

#pr($request->toXml());


$xml = file_get_contents(__DIR__ . './src/Supplier/XmlApi/PurchaseOrder/DH/fixtures/dh-order-entry-response-2');
$response = new \Supplier\XmlApi\PurchaseOrder\DH\Response($xml);
//pr($response);
#$x = $response->parseXml();
pr($response);
