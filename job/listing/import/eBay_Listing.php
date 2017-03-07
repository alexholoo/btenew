<?php

class eBay_Listing extends ListingImporter
{
    public function import()
    {
        $table = 'ebay_bte_listing';
        $file = Filenames::get('ebay.bte.listing');
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

        if (!($fh = @fopen($file, 'rb'))) {
            //$this->log("Failed to open file: $fname");
            return false;
        }

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
