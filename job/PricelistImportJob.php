<?php

class PricelistImportJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($arg = '')
    {
        global $argv;

        if (empty($arg) && isset($argv[1])) {
            $arg = $argv[1];
        }

        if (empty($arg)) {
            // TODO: import all pricelists, see MainTask::chainAction
            $this->log("Missing supplier ID.");
            return;
        }

        $this->importPricelist(strtoupper($arg));
    }

    /** ===== internal methods ===== **/

    protected function importPricelist($supplier)
    {
        $method = "importPricelist_$supplier";

        if (method_exists($this, $method)) {
            $start = microtime(true);
            $this->log("Start import pricelist: $supplier");
            $this->$method();
            $this->log("Import completed, ". $this->elapsed($start) ." seconds elapsed.");
        } else {
            $this->log("Incorrect supplier ID: $supplier");
        }
    }

    protected function importPricelist_DH()
    {
        $columns = include(__DIR__ . '/config/pricelist-columns-dh.php');

        $file = 'E:/BTE/pricelist/DH-ITEMLIST';
        if (($fp = @fopen($file, 'r')) === FALSE) {
            $this->log("Failed to open $file");
            return;
        }

        $items = [];
        while (($fields = fgetcsv($fp, 0, '|'))) {
            if (count($fields) != count($columns)) {
                continue;
            }

            $item = array_combine($columns, $fields);

            $item['sku'] = 'DH-' . $item['sku'];
            $item['rebate_end_date'] = ($item['rebate_end_date'] == '?') ? NULL : date('Y-m-d', strtotime($item['rebate_end_date']));

            $items[] = array_values($item);

            if (count($items) == 1000) {
                try {
                    $sql = $this->genInsertSql('pricelist_dh', $columns, $items);

                    $success = $this->db->execute($sql);
                    if (!$success) {
                        $this->log(__METHOD__ . ' ' .$item['sku']);
                    }

                    $items = [];
                } catch (Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        }

        if (count($items)) {
            try {
                $sql = $this->genInsertSql('pricelist_dh', $columns, $items);
                $success = $this->db->execute($sql);
            } catch (Exception $e) {
                $this->log($e->getMessage());
            }
        }

        fclose($fp);
    }

    protected function importPricelist_SYN()
    {
        $columns = include(__DIR__ . '/config/pricelist-columns-syn.php');

        $file = 'E:/BTE/pricelist/1150897.ap';
        if (($fp = @fopen($file, 'r')) === FALSE) {
            $this->log("Failed to open $file");
            return;
        }

        $items = [];
        while (($fields = fgetcsv($fp, 0, '~'))) {
            if (count($fields) != count($columns)) {
                $this->log('Incorrect number of columns: '. $fields[0]);
                continue;
            }

            $item = array_combine($columns, $fields);

            if ($item['qty'] == 0) {
                //continue;
            }

            $item['sku'] = 'SYN-' . $item['sku'];

            $items[] = array_values($item);

            if (count($items) == 1000) {
                try {
                    $sql = $this->genInsertSql('pricelist_syn', $columns, $items);

                    $success = $this->db->execute($sql);
                    if (!$success) {
                        $this->log(__METHOD__. ' ' .$item['sku']);
                    }

                    $items = [];
                } catch (Exception $e) {
                    $this->log($e->getMessage());
                }
            }
        }

        if (count($items)) {
            try {
                $sql = $this->genInsertSql('pricelist_syn', $columns, $items);
                $success = $this->db->execute($sql);
            } catch (Exception $e) {
                $this->log($e->getMessage());
            }
        }

        fclose($fp);
    }

    protected function importPricelist_AS()
    {
    }

    protected function importPricelist_TD()
    {
    }

    protected function importPricelist_TAK()
    {
    }

    protected function log($line)
    {
        static $first = true;

        $line = date('Y-m-d H:i:s '). $line ."\n";

        if ($first) {
            $first = false;
            $line = "\n". $line;
        }

        echo $line;

        error_log($line, 3, APP_DIR . '/logs/job.log');
    }

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }
/*
    protected function genInsertSql($table, $item)
    {
        $columns = '`' . implode('`, `', array_keys($item)) . '`';
        $values = "('" . implode("', '", array_map('addslashes', array_values($item))) . "')";
        $update = implode(', ', array_map(function($name) { return "$name=VALUES($name)"; }, array_keys($item)));

        $query = "INSERT INTO `$table` ($columns) VALUES $values "
               . "ON DUPLICATE KEY UPDATE $update, is_new=0, updatedon=NOW()";

        return $query;
    }
*/
    protected function genInsertSql($table, $columns, $data)
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

        return $query . implode(",\n", $values) . "\nON DUPLICATE KEY UPDATE $update, is_new=0, updatedon=NOW()";
    }
}

include __DIR__ . '/../public/init.php';

$job = new PricelistImportJob();
$job->run();
