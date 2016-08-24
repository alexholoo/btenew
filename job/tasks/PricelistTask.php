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
        $start = microtime(true);

        echo "Start download pricelist: $supplier\n";

        $info = $this->config->ftp[$supplier];

        $config = [
            'hostname' => $info['Host'],
            'username' => $info['User'],
            'password' => $info['Pass'],
        ];

        $remoteFile = $info['File'];
        $localFile  = $info['Save'];

        $ftp = new FtpClient($config);
        if ($ftp->connect()) {
            $ftp->download($remoteFile, $localFile);
            $ftp->close();
        }

        // if it is zip file, unzip it

        echo "Download completed, ", $this->elapsed($start), " seconds elapsed.\n";
    }

    protected function importPricelist($supplier)
    {
        $start = microtime(true);

        echo "Start import pricelist: $supplier\n";

        $method = "importPricelist_$supplier";

        if (method_exists($this, $method)) {
            $this->$method();
        }

        echo "Import completed, ",  $this->elapsed($start), " seconds elapsed.\n";
    }

    protected function importPricelist_DH()
    {
        $columns = array(
            'stock_status',     // 'StockStatus',
            'qty',              // 'Qty',
            'rebate_flag',      // 'RebateFlag',
            'rebate_end_date',  // 'RebateEndDate',
            'sku',              // 'SKU',
            'mpn',              // 'MPN',
            'upc',              // 'UPC',
            'subcategory',      // 'SubCategory',
            'vendor',           // 'Vendor',
            'cost',             // 'Cost',
            'rebate_amount',    // 'RebateAmount',
            'reserved',         // 'Reserved',
            'freight',          // 'Freight',
            'ship_via',         // 'ShipVia',
            'weight',           // 'Weight',
            'short_desc',       // 'ShortDesc',
            'long_desc',        // 'LongDesc',
        );

        $fp = fopen('./DH-ITEMLIST', 'r');
        if ($fp === FALSE) {
            $this->log('Failed to open DH-ITEMLIST');
            return;
        }

        while (($fields = fgetcsv($fp, 0, '|'))) {
            if (count($fields) != count($columns)) {
                continue;
            }

            $item = array_combine($columns, $fields);

            $item['sku'] = 'DH-' . $item['sku'];
            $item['rebate_end_date'] = ($item['rebate_end_date'] == '?') ? NULL : date('Y-m-d', strtotime($item['rebate_end_date']));

            try {
                $sql = $this->genInsertSql('pricelist_dh', $item);

                $success = $this->db->execute($sql);
                if (!$success) {
                    echo $item['sku'], PHP_EOL;
                }
            } catch (Exception $e) {
                echo $e->getMessage(), PHP_EOL;
            }
        }
        fclose($fp);
    }

    protected function importPricelist_SYN()
    {
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

    protected function genInsertSql($table, $item)
    {
        $columns = '`' . implode('`, `', array_keys($item)) . '`';
        $values = "('" . implode("', '", array_map('addslashes', array_values($item))) . "')";
        $update = implode(', ', array_map(function($name) { return "$name=VALUES($name)"; }, array_keys($item)));

        $query = "INSERT INTO `$table` ($columns) VALUES $values "
               . "ON DUPLICATE KEY UPDATE $update, is_new=0, updatedon=NOW()";

        return $query;
    }
}
