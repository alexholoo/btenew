<?php

namespace Shipment;

class NeweggShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;

    public function __construct($country, $filename)
    {
        $this->filename = $filename;

        if ($country == 'CA') {
            $this->csvtitle = [
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
            ];
        }

        if ($country == 'US') {
            $this->csvtitle = [
            ];
        }
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

    public function write($data)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->csvtitle);
        }

        if (count($data) != count($this->csvtitle)) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data);
    }
}
