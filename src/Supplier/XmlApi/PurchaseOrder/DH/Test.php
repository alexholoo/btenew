<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

$client = new \Supplier\XmlApi\PurchaseOrder\DH\Client($config['dh']);

$request = $client->createRequest();

$order = [ // this comes from ca_order_notes
  'id' => '2754',
  'channel' => 'Amazon-ACA',
  'date' => '2016-08-29',
  'orderId' => '701-3707503-5766610',
  'mgnOrderId' => '',
  'express' => '0',
  'buyer' => 'Sam Wang',
  'address' => '123 Esna Park',
  'city' => 'Toronto',
  'province' => 'ON',
  'postalcode' => 'R3B 0J7',
  'country' => 'CA',
  'phone' => '800-900-1020',
  'email' => 'samwang@email.com',
  'sku' => 'DH-5530287',
  'price' => '87.39',
  'qty' => '1',
  'shipping' => '0.00',
  'mgnInvoiceId' => 'n/a',
  // extra info from user
  'branch' => '',
  'comment' => 'Please ship ASAP!',
];

#$request->addOrder($order);

#pr($request->toXml());


$xml = file_get_contents(__DIR__ . './src/Supplier/XmlApi/PurchaseOrder/DH/fixtures/dh-order-entry-response-2');
$response = new \Supplier\XmlApi\PurchaseOrder\DH\Response($xml);
//pr($response);
#$x = $response->parseXml();
pr($response);
