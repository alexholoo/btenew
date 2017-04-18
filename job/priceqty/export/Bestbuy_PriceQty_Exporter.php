<?php

use Marketplace\Bestbuy\PriceQtyUpdateFile;

class Bestbuy_PriceQty_Exporter extends PriceQty_Exporter
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
        $filename = Filenames::get('bestbuy.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'CA');
    }

    public function exportToFile($file, $site)
    {
        $bestbuyService = $this->di->get('bestbuyService');

        foreach ($this->items as $sku) {
            $info = $bestbuyService->findSku($sku, $site);
            if ($info) {
                $file->write([ $sku, $info['Price'], 0 ]);
            }
        }
    }
}
