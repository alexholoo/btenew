<?php

class eBay_PriceQty_Uploader extends PriceQty_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        $filename = Filenames::get('ebay.odo.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\eBay\Client('odo');
            $this->updatePriceQty($client, $filename);
            File::backup($filename);
        }

        $filename = Filenames::get('ebay.gfs.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\eBay\Client('gfs');
            $this->updatePriceQty($client, $filename);
            File::backup($filename);
        }
    }

    protected function updatePriceQty($client, $filename)
    {
    }
}
