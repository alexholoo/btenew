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
        $file = new PriceQtyUpdateFileCA($filename);
        $this->exportToFileCA($file);

        $filename = Filenames::get('newegg.us.priceqty');
        $file = new PriceQtyUpdateFileUS($filename);
        $this->exportToFileUS($file);
    }

    public function exportToFileCA($file)
    {
        $neweggService = $this->di->get('neweggService');

        foreach ($this->items as $sku) {
            $info = $neweggService->findSku($sku, 'CA');
            if ($info) {
                $price = isset($info['selling_price']) ? $info['selling_price'] : 9999;
                $file->write([ $sku, $price, 0 ]);
            }
        }
    }

    public function exportToFileUS($file)
    {
        $neweggService = $this->di->get('neweggService');

        foreach ($this->items as $sku) {
            $info = $neweggService->findSku($sku, 'US');
            if ($info) {
                $price = isset($info['selling_price']) ? $info['selling_price'] : 9999;
                $file->write([ $sku, $price, 0 ]);
            }
        }
    }
}
