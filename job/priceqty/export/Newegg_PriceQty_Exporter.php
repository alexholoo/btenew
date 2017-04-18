<?php

use Marketplace\Newegg\PriceQtyUpdateFile;

class Newegg_PriceQty_Exporter extends PriceQty_Exporter
{
    public function run($argv = [])
    {
        try {
            $this->export();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function export()
    {
        $filename = Filenames::get('newegg.ca.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'CA');

        $filename = Filenames::get('newegg.us.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'US');
    }

    public function exportToFile($file, $site)
    {
        $neweggService = $this->di->get('neweggService');

        foreach ($this->items as $sku) {
            $info = $neweggService->findSku($sku, $site);
            if ($info) {
                $price = isset($info['selling_price']) ? $info['selling_price'] : 9999;
                $file->write([ $sku, $price, 0 ]);
            }
        }
    }
}
