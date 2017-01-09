<?php

namespace Shipment;

class MasterShipmentFile
{
    protected $handle;
    protected $filename;
    protected $csvtitle;
    protected $delimiter = "\t";

    public function __construct()
    {
        $this->filename = 'w:/out/shipping/master_shipment.txt';

        if (!PROD) {
            $this->filename = 'E:/BTE/shipping/master_shipment.txt';
        }

        $this->csvtitle = [
            'order-id',
            'order-item-id',
            'quantity',
            'ship-date',
            'carrier-code',
            'carrier-name',
            'tracking-number',
            'ship-method',
            'Shipping_address',
            'Site'
        ];
    }

    public function __destruct()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }

    public function write($data)
    {
        if (!$this->handle) {
            if (file_exists($this->filename)) {
                $this->handle = fopen($this->filename, 'a');
            } else {
                $this->handle = fopen($this->filename, 'w');
                fputcsv($this->handle, $this->csvtitle, $this->delimiter);
            }
        }

        if (count($data) != count($this->csvtitle)) {
            throw new \Exception('Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }

    public function compack()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }

        $this->handle = fopen($this->filename, 'r');

        $lines = array();
        while (($fields = fgetcsv($this->handle, 0, $this->delimiter)) !== FALSE) {

            $line = join (",", $fields);

            if (isset($lines[$line])) {
                continue;
            }

            $lines[$line] = true;

        }

        fclose($this->handle);

        $contents = implode("\r\n", array_keys($lines));
        file_put_contents($this->filename, $contents);
    }
}
