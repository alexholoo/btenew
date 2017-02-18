<?php

namespace Shipment;

class RakutenShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;
    protected $delimiter = "\t";

    public function __construct($country, $filename)
    {
        $this->csvtitle = [
            'receipt-id',
            'receipt-item-id',
            'quantity',
            'tracking-type',
            'tracking-number',
            'ship-date',
        ];

        if ($country == 'CA') { }
        if ($country == 'US') { }

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
            throw new \Exception('Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
