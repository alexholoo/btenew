<?php

include 'classes/Job.php';

class AmazonListingImportJob extends Job
{
    public function run($args = [])
    {
        $this->log('Importing amazon_ca_listings');
        $file = 'w:/data/csv/amazon/amazon_ca_listings.txt';
        $table = 'amazon_ca_listings';
        $this->importAmazonListings($file, $table);

        $this->log('Importing amazon_us_listings');
        $file = 'w:/data/csv/amazon/amazon_us_listings.txt';
        $table = 'amazon_us_listings';
        $this->importAmazonListings($file, $table);
    }

    private function importAmazonListings($file, $table)
    {
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
                echo $e->getMessage(), EOL;
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

    private function genInsertSql($table, $columns, $data)
    {
        $columnList = '`' . implode('`, `', $columns) . '`';

        $query = "INSERT INTO `$table` ($columnList) VALUES\n";

        $values = array();

        foreach($data as $row) {
            foreach($row as &$val) {
                $val = addslashes($val);
            }
            $values[] = "('" . implode("', '", $row). "')";
        }

        $update = implode(', ',
            array_map(function($name) {
                return "`$name`=VALUES(`$name`)";
            }, $columns)
        );

        return $query . implode(",\n", $values);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonListingImportJob();
$job->run($argv);
