<?php

class eBay_Listing_Importer extends Listing_Importer
{
    public function import()
    {
        $table = 'ebay_gfs_listing';
        $file = Filenames::get('ebay.gfs.listing');
        $this->importListings($file, $table);

        $table = 'ebay_odo_listing';
        $file = Filenames::get('ebay.odo.listing');
        $this->importListings($file, $table);
    }

    private function importListings($file, $table)
    {
        $listings = $this->getListings($file);

        if (!$listings) {
            return;
        }

        $this->db->execute("TRUNCATE TABLE $table");

        $columns = $this->getColumns();

        $chunks = array_chunk($listings, 1000);
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
            'item_id',
            'title',
            'price',
            'qty',
        ];
    }

    /**
     * For reference only
     */
    private function getCsvHeader()
    {
        return [
            "SKU",
            "Item ID",
            "Title",
            "Fixed Price",
            "Quantity",
        ];
    }
}
