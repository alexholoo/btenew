<?php

class Bestbuy_Listing extends ListingDownloader
{
    public function download()
    {
        $client = new Marketplace\Bestbuy\Client();

        $offset = 0;
        $max = 100;

        $filename = Filenames::get('bestbuy.listing');

        $fp = fopen($filename, 'w');

        fputcsv($fp, [
            'offer_id',
            'active',
            'shop_sku',
            'upc',
            'product_sku',
            'price',
            'quantity',
            'state_code',
            'category_code',
            'category_label',
            'product_title'
        ]);

        do {
            $res = $client->listOffers($offset, $max);

            foreach ($res->offers as $offer) {
                $upc = '';
                if (isset($offer->product_references[0])) {
                    $upc = $offer->product_references[0]->reference;
                }

                $active = $offer->active;
                if (!$active) {
                    $active = 0;
                }

                fputcsv($fp, [
                    $offer->offer_id,
                    $active,
                    $offer->shop_sku,
                    $upc,
                    $offer->product_sku,
                    $offer->price,
                    $offer->quantity,
                    $offer->state_code,
                    $offer->category_code,
                    $offer->category_label,
                    $offer->product_title,
                ]);
            }

            $offset += $max;

        } while ($offset < $res->total_count);

        fclose($fp);
    }
}
