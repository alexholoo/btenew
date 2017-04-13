<?php

namespace Shipment;

class EbayShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;

    public function __construct($filename)
    {
        $this->csvtitle = [
            'recordNumber',
            'orderID',
            'shipDate',
            'carrier',
            'trackingNumber',
            'transactionID'
        ];

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

    public function write($data)
    {
        if (!$this->handle) {
            $this->handle = fopen($this->filename, 'w');
            fputcsv($this->handle, $this->csvtitle);
        }

        return fputcsv($this->handle, [
            $data['recordNumber'],
            $data['orderID'],
            $data['shipDate'],
            $data['carrier'],
            $data['trackingNumber'],
            $data['transactionID']
        ]);
    }
}
