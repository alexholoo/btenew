<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

$client = new \Supplier\XmlApi\PurchaseOrder\Synnex\Client($config['synnex']);

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
  'sku' => 'SYN-357869',
  'price' => '87.39',
  'qty' => '1',
  'shipping' => '0.00',
  'mgnInvoiceId' => 'n/a',
  // extra info from user
  'branch' => '',
  'comment' => 'Please ship ASAP!',
];



$request->addOrder($order);
//dpr($request->toXml());

$response = $client->sendRequest($request);
//pr($response->getXmlDoc());
pr($response->getStatus());
pr($response->getOrders());

#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-5.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-6.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-7.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-8.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-9.xml');

#$response = new \Supplier\XmlApi\PurchaseOrder\Synnex\Response($xml);
//pr($response);
#$x = $response->parseXml();
#pr($x);
