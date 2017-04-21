<?php

namespace Marketplace\Newegg;

class PriceQtyUpdateFileCA
{
    protected $filename;
    protected $delimiter = ",";
    protected $handle;
    protected $columns = [
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
    protected $setting = 'Overwrite=No,,"* Changing the setting to ""Overwrite=Yes"" '
                       . 'will have the added effect of deactivating all of your items '
                       . 'from the website except for those listed on this datafeed. If this is not '
                       . 'intended, keep ""Overwrite=No"".",,,,,,,,';

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
                fputs($this->handle, $this->setting."\n");
                fputcsv($this->handle, $this->columns, $this->delimiter);
            }
        }

        if (count($data) != count($this->columns)) {
            throw new \Exception(__METHOD__.': Wrong number of elements: '. var_export($data, true));
        }

        return fputcsv($this->handle, $data, $this->delimiter);
    }
}
