<?php

class StdOrderReportFile
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
            fgetcsv($this->handle, 0 , $this->delimeter); // skip the header
        }

        $fields = fgetcsv($this->handle, 0 , $this->delimeter);
        if ($fields) {
            return array_combine($this->getHeader(), $fields);
        }

        return $fields;
    }

    public function write($order)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->getHeader(), $this->delimeter);
        }

        if (count($order) != count($this->getHeader())) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($order, true));
        }

        return fputcsv($this->handle, $order, $this->delimeter);
    }

    public function getHeader()
    {
        $headers = [
            'CA' => [
                'order-id',
                'order-item-id',
                'purchase-date',
                'payments-date',
                'buyer-email',
                'buyer-name',
                'buyer-phone-number',
                'sku',
                'product-name',
                'quantity-purchased',
                'currency',
                'item-price',
                'item-tax',
                'shipping-price',
                'shipping-tax',
                'ship-service-level',
                'recipient-name',
                'ship-address-1',
                'ship-address-2',
                'ship-address-3',
                'ship-city',
                'ship-state',
                'ship-postal-code',
                'ship-country',
                'ship-phone-number',
                'item-promotion-discount',
                'item-promotion-id',
                'ship-promotion-discount',
                'ship-promotion-id',
                'delivery-start-date',
                'delivery-end-date',
                'delivery-time-zone',
                'delivery-Instructions',
                'sales-channel',
            ],

            'US' => [
                'order-id',
                'order-item-id',
                'purchase-date',
                'payments-date',
                'buyer-email',
                'buyer-name',
                'buyer-phone-number',
                'sku',
                'product-name',
                'quantity-purchased',
                'currency',
                'item-price',
                'item-tax',
                'shipping-price',
                'shipping-tax',
                'ship-service-level',
                'recipient-name',
                'ship-address-1',
                'ship-address-2',
                'ship-address-3',
                'ship-city',
                'ship-state',
                'ship-postal-code',
                'ship-country',
                'ship-phone-number',
                'delivery-start-date',
                'delivery-end-date',
                'delivery-time-zone',
                'delivery-Instructions',
                'sales-channel',
                'order-channel',
                'order-channel-instance',
                'external-order-id',
                'purchase-order-number',
            ],
        ];

        return $headers[$this->site];
    }
}
