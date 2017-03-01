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

        if (count($data) != count($this->columns)) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data);
    }
}
