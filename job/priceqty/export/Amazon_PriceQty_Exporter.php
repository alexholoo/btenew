<?php

use Marketplace\Amazon\Feeds\PriceQtyUpdateFile;

class Amazon_PriceQty_Exporter extends PriceQty_Exporter
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
        $filename = Filenames::get('amazon.ca.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'CA');

        $filename = Filenames::get('amazon.us.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'US');
    }

    public function exportToFile($file, $site)
    {
        $amazonService = $this->di->get('amazonService');

        foreach ($this->items as $sku) {
            $info = $amazonService->findSku($sku, $site);
            if ($info) {
                $file->write([ $sku, $info['price'], 0 ]);
            }
        }
    }
}
