<?php

namespace Marketplace\Newegg;

class PriceQtyUpdateFileCA
{
    protected $filename;
    protected $delimiter = ",";
    protected $handle;
    protected $columns  = [
                "Seller Part #",
                "NE Item #",
                "Currency",
                "MSRP",
                "MAP",
                "Checkout MAP",
                "Selling Price",
                "Inventory",
                "Fulfillment option",
                "Shipping",
                "Activation Mark"
            ];

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    public function close()
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
                if ($this->columns) {
                    fputs($this->handle, 'Overwrite=No,,"* Changing the setting to ""Overwrite=Yes"" will have the added effect of deactivating all of your items from the website except for those listed on this datafeed. If this is not intended, keep ""Overwrite=No"".",,,,,,,,'."\n");
                    fputcsv($this->handle, $this->columns, $this->delimiter);
                }
            }
        }

        if (count($data) != count($this->columns)) {
            throw new \Exception(get_called_class().': Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
