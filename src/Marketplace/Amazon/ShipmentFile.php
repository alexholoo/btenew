<?php

namespace Marketplace\Amazon;

class ShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;
    protected $delimiter = "\t";

    public function __construct($filename)
    {
        $this->csvtitle = [
            'order-id',
            'order-item-id',
            'quantity',
            'ship-date',
            'carrier-code',
            'carrier-name',
            'tracking-number',
            'ship-method',
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
            fputcsv($this->handle, $this->csvtitle, $this->delimiter);
        }

        if (count($data) != count($this->csvtitle)) {
            throw new \Exception(__METHOD__. ' Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
