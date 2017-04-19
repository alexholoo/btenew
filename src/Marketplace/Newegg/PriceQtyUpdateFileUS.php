<?php

namespace Marketplace\Newegg;

class PriceQtyUpdateFileUS
{
    protected $filename;
    protected $delimiter = ",";
    protected $handle;
    protected $columns = [
                "Seller Part #",
                "NE Item #",
                "Warehouse Location",
                "Fulfillment Option",
                "Inventory"
              ];

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

    public function write($data)
    {
        if (!$this->handle) {
            if (file_exists($this->filename)) {
                $this->handle = fopen($this->filename, 'a');
            } else {
                $this->handle = fopen($this->filename, 'w');
                fputs($this->handle, "Version=2.0\n");
                fputcsv($this->handle, $this->columns, $this->delimiter);
            }
        }

        if (count($data) != count($this->columns)) {
            throw new \Exception(__METHOD__.': Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
