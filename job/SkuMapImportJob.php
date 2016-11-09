<?php

class SkuMapImportJob
{
    protected $asin = [];
    protected $upc  = [];
    protected $mpn  = [];

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($arg = '')
    {
        $this->collectInfo();

        $this->importAsin();
        $this->importMpn();
        $this->importUpc();
    }

    protected function collectInfo()
    {
       #$file  = 'w:/data/amazon_ca_sku_asin_table.csv';
        $file  = 'E:/BTE/import/amazon_ca_sku_asin_table.csv';
        $this->collectAsin($file);

       #$file  = 'w:/data/amazon_us_sku_asin_table.csv';
        $file  = 'E:/BTE/import/amazon_us_sku_asin_table.csv';
        $this->collectAsin($file);

        $this->collectMpn();
        $this->collectUpc();
    }

    protected function collectAsin($file)
    {
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Loading $file\n";

        fgetcsv($fh); // skip the first line

        while (($fields = fgetcsv($fh))) {
            $asin = $fields[0];
            $skus = $fields[1];
            $list = explode('|', $skus);
            foreach ($list as $sku) {
                $this->asin[$sku] = [ $sku, $asin ];
            }
        }

        fclose($fh);
    }

    protected function collectMpn()
    {
       #$file = 'w:/data/master_upc_mpn.csv';
        $file = 'E:/BTE/import/master_upc_mpn.csv';
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Loading $file\n";

        fgetcsv($fh); // skip the first line

        while (($fields = fgetcsv($fh))) {
            $sku = $fields[0];
            $upc = trim($fields[1]);
            $mpn = trim($fields[2]);

            if ($upc > 0) {
                $this->upc[$sku] = [ $sku, $upc ];
            }
            if ($mpn) {
                $this->mpn[$sku] = [ $sku, $mpn ];
            }
        }

        fclose($fh);
    }

    protected function collectUpc()
    {
        # /w/data/master_upc_mpn.csv
        # done in $this->collectMpn()
    }

    protected function importAsin()
    {
        $table   = 'sku_asin_map';
        $columns = [ 'sku', 'asin' ];
        $data    = $this->asin;

        echo "Importing $table ... ";

        $start = microtime(true);

        try {
            $this->db->execute("TRUNCATE TABLE $table");

            $chunks = array_chunk($data, 5000);
            foreach ($chunks as $chunk) {
                $sql = $this->genInsertSql($table, $columns, $chunk);
                $this->db->execute($sql);
            }
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }

        $count = count($data);
        $duration = round(microtime(true) - $start, 2);

        echo "$count sku-asin maps imported in $duration secs\n";
    }

    protected function importMpn()
    {
        $table   = 'sku_mpn_map';
        $columns = [ 'sku', 'mpn' ];
        $data    = $this->mpn;

        echo "Importing $table ... ";

        $start = microtime(true);

        try {
            $this->db->execute("TRUNCATE TABLE $table");

            $chunks = array_chunk($data, 5000);
            foreach ($chunks as $chunk) {
                $sql = $this->genInsertSql($table, $columns, $chunk);
                $this->db->execute($sql);
            }
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }

        $count = count($data);
        $duration = round(microtime(true) - $start, 2);

        echo "$count sku-mpn maps imported in $duration secs\n";
    }

    protected function importUpc()
    {
        $table   = 'sku_upc_map';
        $columns = [ 'sku', 'upc' ];
        $data    = $this->upc;

        echo "Importing $table ... ";

        $start = microtime(true);

        try {
            $this->db->execute("TRUNCATE TABLE $table");

            $chunks = array_chunk($data, 5000);
            foreach ($chunks as $chunk) {
                $sql = $this->genInsertSql($table, $columns, $chunk);
                $this->db->execute($sql);
            }
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }

        $count = count($data);
        $duration = round(microtime(true) - $start, 2);

        echo "$count sku-upc maps imported in $duration secs\n";
    }

    public function genInsertSql($table, $columns, $data)
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

        return $query . implode(",\n", $values) . "\nON DUPLICATE KEY UPDATE " . $update . ';';
    }
}

include __DIR__ . '/../public/init.php';

$job = new SkuMapImportJob();
$job->run();