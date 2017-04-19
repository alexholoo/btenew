<?php

use Marketplace\Rakuten\PriceQtyUpdateFile;

class Rakuten_PriceQty_Exporter extends PriceQty_Exporter
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
        $filename = Filenames::get('rakuten.us.priceqty');
        $file = new PriceQtyUpdateFile($filename);
        $this->exportToFile($file, 'US');
    }

    public function exportToFile($file, $site)
    {
        $rakutenService = $this->di->get('rakutenService');

        foreach ($this->items as $sku) {
            $info = $rakutenService->findSku($sku, $site);
            if ($info) {
                $info['Quantity'] = 0;
                $info['ProductIdType'] = 0;
                $info['Description'] = '';
                $file->write([
                    $info['ListingId'],
                    $info['ReferenceId'],
                    $info['ProductIdType'],
                    $info['ItemConditionId'],
                    $info['Price'],
                    $info['MAP'],
                    $info['Quantity'],
                    $info['OfferExpeditedShipping'],
                    $info['Description'],
                    $info['ShippingRateStandard'],
                    $info['ShippingRateExpedited'],
                    $info['ShippingLeadTime'],
                    $info['Sku'],
                ]);
            }
        }
    }
}
