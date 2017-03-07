<?php

include 'classes/Job.php';

class AmazonListingImportJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $table = 'amazon_ca_listings';
        $file = 'E:/BTE/import/amazon_ca_listings.txt';
       #$file = 'w:/data/csv/amazon/amazon_ca_listings.txt';
        $this->importAmazonListings($file, $table);

        $table = 'amazon_us_listings';
        $file = 'E:/BTE/import/amazon_us_listings.txt';
       #$file = 'w:/data/csv/amazon/amazon_us_listings.txt';
        $this->importAmazonListings($file, $table);
    }

    private function importAmazonListings($file, $table)
    {
        $this->log("Importing $file");

        $listings = $this->getListings($file);

        if (!$listings) {
            return;
        }

        $this->db->execute("TRUNCATE TABLE $table");

        $columns = [ 'name', 'sku', 'price', 'asin' ];

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

        if (!($fh = @fopen($file, 'rb'))) {
            //$this->log("Failed to open file: $fname");
            return false;
        }

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
}

include __DIR__ . '/../public/init.php';

$job = new AmazonListingImportJob();
$job->run($argv);
