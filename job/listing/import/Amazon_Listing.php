<?php

class Amazon_Listing extends ListingImporter
{
    public function import()
    {
        $table = 'amazon_ca_listings';
        $file = Filenames::get('amazon.ca.listing');
        $this->importAmazonListings($file, $table);

        $table = 'amazon_us_listings';
        $file = Filenames::get('amazon.us.listing');
        $this->importAmazonListings($file, $table);
    }

    private function importAmazonListings($file, $table)
    {
        $listings = $this->getListings($file);

        if (!$listings) {
            return;
        }

        $this->db->execute("TRUNCATE TABLE $table");

        $columns = $this->getColumns();

        $chunks = array_chunk($listings, 2000);
        foreach ($chunks as $chunk) {
            try {
                $sql = $this->genInsertSql($table, $columns, $chunk);
                $this->db->execute($sql);
            } catch (\Exception $e) {
                $this->error($e->getMessage() .' in '. __METHOD__);
            }
        }
    }

    private function getListings($file)
    {
        $listings = [];

        if (!file_exists($file)) {
            //$this->log("Failed to open file: $fname");
            return $listings;
        }

        $fh = fopen($file, 'rb');

        $title = fgetcsv($fh, 0, "\t");

        while (($data = fgetcsv($fh, 0, "\t"))) {
            $fields = array_combine($title, $data);
            $listings[] = [
                'name'  => $fields['item-name'],
                'sku'   => $fields['seller-sku'],
                'price' => $fields['price'],
                'asin'  => $fields['asin1'],
            ];
        }

        fclose($fh);

        return $listings;
    }

    private function getColumns()
    {
        return [
            'name',
            'sku',
            'price',
            'asin'
        ];
    }

    private function getCsvHeader()
    {
        return [
            'US' => [
                'item-name',
                'item-description',
                'listing-id',
                'seller-sku',
                'price',
                'quantity',
                'open-date',
                'image-url',
                'item-is-marketplace',
                'product-id-type',
                'zshop-shipping-fee',
                'item-note',
                'item-condition',
                'zshop-category1',
                'zshop-browse-path',
                'zshop-storefront-feature',
                'asin1',
                'asin2',
                'asin3',
                'will-ship-internationally',
                'expedited-shipping',
                'zshop-boldface',
                'product-id',
                'bid-for-featured-placement',
                'add-delete',
                'pending-quantity',
                'fulfillment-channel',
                'Business Price',
                'Quantity Price Type',
                'Quantity Lower Bound 1',
                'Quantity Price 1',
                'Quantity Lower Bound 2',
                'Quantity Price 2',
                'Quantity Lower Bound 3',
                'Quantity Price 3',
                'Quantity Lower Bound 4',
                'Quantity Price 4',
                'Quantity Lower Bound 5',
                'Quantity Price 5',
                'merchant-shipping-group',
            ],

            'CA' => [
                'item-name',
                'item-description',
                'listing-id',
                'seller-sku',
                'price',
                'quantity',
                'open-date',
                'image-url',
                'item-is-marketplace',
                'product-id-type',
                'zshop-shipping-fee',
                'item-note',
                'item-condition',
                'zshop-category1',
                'zshop-browse-path',
                'zshop-storefront-feature',
                'asin1',
                'asin2',
                'asin3',
                'will-ship-internationally',
                'expedited-shipping',
                'zshop-boldface',
                'product-id',
                'bid-for-featured-placement',
                'add-delete',
                'pending-quantity',
                'fulfillment-channel',
                'merchant-shipping-group',
            ]
        ];
    }
}
