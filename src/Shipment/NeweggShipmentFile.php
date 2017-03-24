<?php

namespace Shipment;

use Marketplace\Newegg\StdOrderListFile;

class NeweggShipmentFile extends StdOrderListFile
{
    // Shipment file has the same format as OrderList file

    public function getHeader()
    {
        $headers = [
            'CA' => [
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
                'Order Discount Amount',
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
            ],

            'US' => [
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
                'Extend VAT',
                'Extend Duty',
                'Order Shipping Total',
                'Order Discount Amount',
                'Sales Tax',
                'VAT Total',
                'Duty Total',
                'Order Total',
                'Quantity Ordered',
                'Quantity Shipped',
                'Actual Shipping Carrier',
                'Actual Shipping Method',
                'Tracking Number',
                'ShipDate',
            ],
        ];

        return $headers[$this->site];
    }
}
