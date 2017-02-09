<?php

require 'database.php';

$fp = fopen('e:/Amazon_Export_2016121215348.csv', 'r');

$columns = fgetcsv($fp);

$items = [];
while (($fields = fgetcsv($fp)) !== false) {
    $data = array_combine($columns, $fields);
    try {
        $items[] = [
			'sku'         => $data['SellerSKU'],
			'asin'        => $data['Product ID'],
			'upc'         => $data['UPC'],
			'title'       => $data['Title'],
			'image_small' => $data['ImageURLSmall'],
			'image_big'   => $data['ImageURLBig'],
		];

		if (count($items) >= 2000) {
			$sql = genInsertSql('amazon_image', ['sku','asin','upc','title','image_small','image_big'], $items);
			$db->execute($sql);
			$items = [];
		}

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

if (count($items) > 0) {
	$sql = genInsertSql('amazon_image', ['sku','asin','upc','title','image_small','image_big'], $items);
	$db->execute($sql);
	$items = [];
}

fclose($fp);
/*
Array
(
    [InventoryAction] => Modify
    [Site] => Amazon.com
    [SellerSKU] => AS-100015
    [Location] =>
    [Location Priority] =>
    [Title] => Intel Ethernet Server Adapter I340-T4 1Gbps RJ-45 Copper, PCI Express 2.0 x 4 Lane, OEM packaging
    [Condition] => New
    [Condition Description] => No Sales Tax. Part Number: E1G44HTBLK
    [Quantity] => 0
    [MinListing Buffer] =>
    [MaxListing Buffer] =>
    [Price] => 356.51
    [Product ID] => B003A7LKOU
    [Product Type] => ASIN
    [UPC] => 735858212465
    [ItemWeight] => 0.1
    [ItemHeight] => 0.8
    [ItemWidth] => 5.4
    [ItemLength] => 8
    [ImageURLSmall] => http://ecx.images-amazon.com/images/I/11jWK4OdGoL._SL75_.jpg
    [ImageURLBig] => http://ecx.images-amazon.com/images/I/11jWK4OdGoL._SX500_.jpg
    [Description] =>
    [Group Name] =>
    [Cost] => 329.08
    [Price (preferred)] => 376.51
    [Price (minimum)] => 356.51
    [Price (maximum)] => 376.51
    [MAP Price] =>
    [Price (retail)] =>
    [Pricing Name] => None
    [Vendor Name] =>
    [Model Number] =>
    [Brand] =>
    [Attribute1] =>
    [Attribute2] =>
    [Attribute3] =>
    [Attribute4] =>
    [Attribute5] =>
    [Attribute6] =>
    [Attribute7] =>
    [Attribute8] =>
    [LeadtimeToShip] =>
    [Store Link] =>
    [Mobile Link] =>
    [SalePrice] =>
    [SaleStartDate] =>
    [SaleEndDate] =>
    [RestockDate] =>
    [Fulfilled By] => Merchant
)
*/
