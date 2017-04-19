<?php

use Marketplace\Newegg\PriceQtyUpdateFileCA;
use Marketplace\Newegg\PriceQtyUpdateFileUS;

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
                $info['inventory'] = 0;
                $file->write([
                    $info['sku'],
                    $info['newegg_item_id'],
                    $info['currency'],
                    $info['MSRP'],
                    $info['MAP'],
                    $info['checkout_map'],
                    $info['selling_price'],
                    $info['inventory'],
                    $info['fulfillment_option'],
                    $info['shipping'],
                    $info['activation_mark'],
                ]);
            }
        }
    }

    public function exportToFileUS($file)
    {
        $neweggService = $this->di->get('neweggService');

        foreach ($this->items as $sku) {
            $info = $neweggService->findSku($sku, 'US');
            if ($info) {
                $info['inventory'] = 0;
                $file->write([
                    $info['sku'],
                    $info['newegg_item_id'],
                    $info['warehouse_location'],
                    $info['fulfillment_option'],
                    $info['inventory'],
                ]);
            }
        }
    }
}
