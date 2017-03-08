<?php

class Newegg_Listing extends ListingImporter
{
    public function import()
    {
        $table = 'newegg_ca_listing';
        $filename = Filenames::get('newegg.ca.listing');

        $this->importListings($filename, $table);
    }

    private function importListings($file, $table)
    {
        //$this->log("Importing $file");

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

        fgetcsv($fh); // skip first line

        $title = fgetcsv($fh);

        while (($fields = fgetcsv($fh))) {
            $listings[] = $fields;
        }

        fclose($fh);

        return $listings;
    }

    private function getColumns()
    {
        return [
            'sku',
            'newegg_item_id',
            'currency',
            'MSRP',
            'MAP',
            'checkout_map',
            'selling_price',
            'inventory',
            'fulfillment_option',
            'shipping',
            'activation_mark',
        ];
    }

    private function getCsvHeader()
    {
        return [
            'Seller Part #',
            'NE Item #',
            'Currency',
            'MSRP',
            'MAP',
            'Checkout MAP',
            'Selling Price',
            'Inventory',
            'Fulfillment Option',
            'Shipping',
            'Activation Mark'
        ];
    }
}
