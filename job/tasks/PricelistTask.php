<?php

class PricelistTask extends \Phalcon\Cli\Task
{
    public function mainAction()
    {
        echo "Missing action.\n";
    }

    /**
     * @param array $params
     */
    public function downloadAction(array $params)
    {
        if (empty($params)) {
            // TODO: download all pricelists, see MainTask::chainAction
            echo "Missing supplier ID.\n";
            return;
        }

        $this->downloadPricelist(strtoupper($params[0]));
    }

    /**
     * @param array $params
     */
    public function importAction(array $params)
    {
        if (empty($params)) {
            // TODO: import all pricelists, see MainTask::chainAction
            echo "Missing supplier ID.\n";
            return;
        }

        $this->importPricelist(strtoupper($params[0]));
    }

    /** ===== internal methods ===== **/

    protected function downloadPricelist($supplier)
    {
        $config = $this->config->ftp[$supplier];

        if (!$config) {
            echo "Incorrect supplier ID: $supplier\n";
            return;
        }

        $start = microtime(true);

        echo "Start download pricelist: $supplier\n";

        $account = [
            'hostname' => $config['Host'],
            'username' => $config['User'],
            'password' => $config['Pass'],
        ];

        $remoteFile = $config['File'];
        $localFile  = $config['Save'];

        $ftp = new FtpClient();
        if ($ftp->connect($account)) {
            $ftp->download($remoteFile, $localFile);
            $ftp->close();
        }

        // if it is zip file, unzip it

        echo "Download completed, ", $this->elapsed($start), " seconds elapsed.\n";
    }

    protected function importPricelist($supplier)
    {
        $method = "importPricelist_$supplier";

        if (method_exists($this, $method)) {
            $start = microtime(true);
            echo "Start import pricelist: $supplier\n";
            $this->$method();
            echo "Import completed, ",  $this->elapsed($start), " seconds elapsed.\n";
        } else {
            echo "Incorrect supplier ID: $supplier\n";
        }
    }

    protected function importPricelist_DH()
    {
        $columns = include(__DIR__ . '/pricelist-columns-dh.php');

        $fp = fopen(__DIR__ . '/DH-ITEMLIST', 'r');
        if ($fp === FALSE) {
            $this->log('Failed to open DH-ITEMLIST');
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
                        echo $item['sku'], PHP_EOL;
                    }

                    $items = [];
                } catch (Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                }
            }
        }

        if (count($items)) {
            try {
                $sql = $this->genInsertSql('pricelist_dh', $columns, $items);
                $success = $this->db->execute($sql);
            } catch (Exception $e) {
                echo $e->getMessage(), PHP_EOL;
            }
        }

        fclose($fp);
    }

    protected function importPricelist_SYN()
    {
        $columns = include(__DIR__ . '/pricelist-columns-syn.php');

        $fp = fopen(__DIR__ . '/1150897.ap', 'r');

        if ($fp === FALSE) {
            $this->log('Failed to open 1150897.ap');
            return;
        }

        $items = [];
        while (($fields = fgetcsv($fp, 0, '~'))) {
            if (count($fields) != count($columns)) {
                echo 'Incorrect number of columns: ', $fields[0], PHP_EOL;
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
                        echo $item['sku'], PHP_EOL;
                    }

                    $items = [];
                } catch (Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                }
            }
        }

        if (count($items)) {
            try {
                $sql = $this->genInsertSql('pricelist_syn', $columns, $items);
                $success = $this->db->execute($sql);
            } catch (Exception $e) {
                echo $e->getMessage(), PHP_EOL;
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
        error_log(date('Y-m-d H:i:s ') . $line . "\n", 3, './tasks.log');
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
