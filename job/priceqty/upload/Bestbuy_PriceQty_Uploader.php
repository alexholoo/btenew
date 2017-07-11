<?php

use Toolkit\File;

class Bestbuy_PriceQty_Uploader extends PriceQty_Uploader
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
        $filename = Filenames::get('bestbuy.priceqty');
        if (file_exists($filename)) {
            $this->uploadOffers($filename);
            File::archive($filename);
        }
    }

    public function uploadOffers($filename)
    {
        $client = new Marketplace\Bestbuy\Client();

        $offers = $this->csvToArray($filename, ';');
        $chunks = array_chunk($offers, 1000);

        foreach ($chunks as $chunk) {
            $data['offers'] = [];

            foreach ($chunk as $offer) {
                $data['offers'][] = [
                    'available_ended'      => null,
                    'available_started'    => null,
                    'description'          => '',
                    'internal_description' => '',
                    'min_quantity_alert'   => '',
                    'price'                => floatval($offer['price']),
                    'product_id'           => '',
                    'product_id_type'      => '',
                    'quantity'             => intval($offer['quantity']),
                    'shop_sku'             => $offer['sku'],
                    'state_code'           => '11',
                    'update_delete'        => 'update',
                ];
            }

            $res = $client->updateOffers($data);
            //print_r($res->import_id);
        }
    }
}
