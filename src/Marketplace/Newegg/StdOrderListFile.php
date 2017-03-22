<?php

namespace Marketplace\Newegg;

class StdOrderListFile
{
    protected $site;
    protected $handle;
    protected $filename;

    public function __construct($filename, $site)
    {
        $this->filename = $filename;
        $this->site = $site;
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function read()
    {
        if (!$this->handle) {
            if (!file_exists($this->filename)) {
                return false;
            }
            $this->handle = fopen($this->filename, 'r');
            fgetcsv($this->handle); // skip the header
        }

        $fields = fgetcsv($this->handle);
        if ($fields) {
            return array_combine($this->getHeader(), $fields);
        }

        return $fields;
    }

    public function write($order)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->getHeader());
        }

        if (count($order) != count($this->getHeader())) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($order, true));
        }

        return fputcsv($this->handle, $order);
    }

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
                'Sales Tax',
                'VAT Total',
                'Duty Total',
                'Order Total',
                'Quantity Ordered',
                'Quantity Shipped',
                'Actual Shipping Carrier',
                'Actual Shipping Method',
                'Tracking Number',
            ],
        ];

        return $headers[$this->site];
    }
}
