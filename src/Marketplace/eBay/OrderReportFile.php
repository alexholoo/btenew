<?php

class OrderReportFile
{
    protected $handle;
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
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
        return [
            // Order
            'OrderID',
            'Status',
            'BuyerUsername',
            'DatePaid',
            'Currency',
            'AmountPaid',
            'SalesTaxAmount',
            'ShippingService',
            'ShippingServiceCost',
            // Item
            'SKU',
            'QuantityPurchased',
            'TransactionID',
            'TransactionPrice',
           #'Tracking',
            'ItemID',
            'Email',
           #'RecordNumber',
            'ProductName',
            // Address
            'Name',
            'Address',
            'Address2',
            'City',
            'Province',
            'PostalCode',
            'Country',
            'Phone'
        ];
    }
}