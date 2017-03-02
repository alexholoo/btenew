<?php

namespace Marketplace\Rakuten;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class OrderList
{
    protected $filename;

    // this comes from OrderList
    protected static $title = [
        'SellerShopperNumber',
        'Receipt_ID',
        'Receipt_Item_ID',
        'ListingID',
        'Date_Entered',
        'Sku',
        'ReferenceId',
        'Quantity',
        'Qty_Shipped',
        'Qty_Cancelled',
        'Title',
        'Price',
        'Product_Rev',
        'Shipping_Cost',
        'ProductOwed',
        'ShippingOwed',
        'Commission',
        'ShippingFee',
        'PerItemFee',
        'Tax_Cost',
        'Bill_To_Company',
        'Bill_To_Phone',
        'Bill_To_Fname',
        'Bill_To_Lname',
        'Email',
        'Ship_To_Name',
        'Ship_To_Company',
        'Ship_To_Street1',
        'Ship_To_Street2',
        'Ship_To_City',
        'Ship_To_State',
        'Ship_To_Zip',
        'ShippingMethodId',
    ];

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public static function getTitle()
    {
        return self::$title;
    }

    public static function getTitleString()
    {
        return implode(',', self::$title);
    }
}
