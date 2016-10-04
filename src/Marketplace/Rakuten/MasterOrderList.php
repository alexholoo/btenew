<?php

namespace Marketplace\Rakuten;

use Toolkit\CsvFileWriter;
use Toolkit\CsvFileReader;

class MasterOrderList
{
    protected $site;
    protected $filename;

    public function __construct($site = 'CA')
    {
        $this->site = strtoupper($site);

        if ($this->site == 'CA') {
            $this->filename = "E:/BTE/orders/rakuten/orders_ca/rakuten_ca_master_orders.csv";
        }

        if ($this->site == 'US') {
            $this->filename = "E:/BTE/orders/rakuten/orders_us/rakuten_us_master_orders.csv";
        }
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function generate()
    {
        if ($this->site == 'CA') {
            $files = glob("E:/BTE/orders/rakuten/orders_ca/23267604_*.*");
        }

        if ($this->site == 'US') {
            $files = glob("E:/BTE/orders/rakuten/orders_us/23267604_*.*");
        }

        $out = fopen($this->filename, "w");
        fwrite($out, OrderList::getTitleString());

        foreach($files as $file){
            $in = fopen($file, "r");
            fgetcsv($in); // skip title row
            while (($fields = fgetcsv($in, 0, "\t")) !== false) {
                if (count($fields) > 1) {
                    fputcsv($out, $fields);
                }
            }
            fclose($in);
        }

        fclose($out);
    }
}
