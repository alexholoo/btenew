<?php

class Bestbuy_PriceQty_Uploader extends PriceQty_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

    public function upload()
    {
        $client = new Marketplace\Bestbuy\Client();

        $filename = Filenames::get('bestbuy.priceqty');

        $offers = $this->csvToArray($filename, ';');
        $chunks = array_chunk($offers, 100);

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
