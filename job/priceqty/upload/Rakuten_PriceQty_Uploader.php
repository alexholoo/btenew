<?php

class Rakuten_PriceQty_Uploader extends PriceQty_Uploader
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
        $filename = Filenames::get('rakuten.us.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\Rakuten\Client('US');
            $client->uploadPriceQty($filename);
            File::backup($filename);
        }
    }
}
