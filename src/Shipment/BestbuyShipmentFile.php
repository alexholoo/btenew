<?php

namespace Shipment;

class BestbuyShipmentFile
{
    protected $handle;
    protected $filename;
    protected $columns;

    public function __construct($filename)
    {
        $this->columns = [
            'orderId',
            'bestbuyId',
            'shipDate',
            'carrierCode',
            'carrierName',
            'shipMethod',
            'trackingNumber',
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
            fputcsv($this->handle, $this->columns);
        }

        return fputcsv($this->handle, [
                $data['orderId'],
                $data['bestbuyId'],
                $data['shipDate'],
                $data['carrierCode'],
                $data['carrierName'],
                $data['shipMethod'],
                $data['trackingNumber'],
            ]
        );
    }
}
