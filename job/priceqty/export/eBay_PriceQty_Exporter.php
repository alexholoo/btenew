<?php

use Marketplace\eBay\PriceQtyUpdateFile;

class eBay_PriceQty_Exporter extends PriceQty_Exporter
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
        $filename = Filenames::get('ebay.odo.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'ODO');

        $filename = Filenames::get('ebay.gfs.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'GFS');
    }

    public function exportToFile($file, $site)
    {
        $ebayService = $this->di->get('ebayService');

        foreach ($this->items as $sku) {
            $info = $ebayService->findSku($sku, $site);
            if ($info) {
                $file->write([ $sku, $info['item_id'], $info['price'], 0 ]);
            }
        }
    }
}
