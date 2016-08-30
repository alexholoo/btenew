<?php

require __DIR__ . '/public/init.php';

$config = include __DIR__ . '/app/config/xmlapi.php';

function pr($d) { print_r($d); echo EOL; }

$client = new \Supplier\XmlApi\PurchaseOrder\Synnex\Client($config['synnex']);

$request = $client->createRequest();

$order = [
    'orderNo' => '11223341',
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
    'sku' => 'SYN-357869',
    'price' => '32.98',
    'qty' => '1',
];

$request->addOrder($order);

pr($request->toXml());
fpr($request->toXml());

$response = $client->sendRequest($request);
fpr($response->getXmlDoc());
pr($response->getXmlDoc());


#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-5.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-6.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-7.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-8.xml');
#$xml = file_get_contents(__DIR__ . '/src/Supplier/XmlApi/PurchaseOrder/Synnex/fixtures/synnex-po-response-9.xml');

#$response = new \Supplier\XmlApi\PurchaseOrder\Synnex\Response($xml);
//pr($response);
#$x = $response->parseXml();
#pr($x);
