<?php

namespace Marketplace\Newegg;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class OrderList
{
    protected $filename;

    // this comes from OrderList
    protected static $title = [
        'Order Number',
        'Order Date & Time',
        'Sales Channel',
        'Fulfillment Option',
        'Ship To Address Line 1',
        'Ship To Address Line 2',
        'Ship To City',
        'Ship To State',
        'Ship To ZipCode',
        'Ship To Country',
        'Ship To First Name',
        'Ship To LastName',
        'Ship To Company',
        'Ship To Phone Number',
        'Order Customer Email',
        'Order Shipping Method',
        'Item Seller Part #',
        'Item Newegg #',
        'Item Unit Price',
        'Extend Unit Price',
        'Item Unit Shipping Charge',
        'Extend Shipping Charge',
        'Order Shipping Total',
        'GST or HST Total',
        'PST or QST Total',
        'Order Total',
        'Quantity Ordered',
        'Quantity Shipped',
        'ShipDate',
        'Actual Shipping Carrier',
        'Actual Shipping Method',
        'Tracking Number',
        'Ship From Address',
        'Ship From City',
        'Ship From State',
        'Ship From Zipcode',
        'Ship From Name',
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
