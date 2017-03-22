<?php

namespace Marketplace\Rakuten;

class StdOrderListFile
{
    protected $site;
    protected $handle;
    protected $filename;
    protected $delimiter = "\t";

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
            fgetcsv($this->handle, 0, $this->delimiter); // skip the header
        }

        $fields = fgetcsv($this->handle, 0, $this->delimiter);
        if ($fields) {
            return array_combine($this->getHeader(), $fields);
        }

        return $fields;
    }

    public function write($order)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->getHeader(), $this->delimiter);
        }

        if (count($order) != count($this->getHeader())) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($order, true));
        }

        return fputcsv($this->handle, $order, $this->delimiter);
    }

    public function getHeader()
    {
        $headers = [
            'CA' => [
                //
            ],

            'US' => [
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
            ],
        ];

        return $headers[$this->site];
    }
}
