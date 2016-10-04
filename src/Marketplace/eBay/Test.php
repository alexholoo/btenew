<?php

include './public/init.php';

$config = include './app/config/ebay.php';

$client = new Marketplace\eBay\Client($config['bte']);

$res = $client->geteBayOfficialTime();
print_r($res);

#$res = $client->getMyeBaySelling(1);
#print_r($res);

#$res = $client->geteBayDetails();
#print_r($res);
