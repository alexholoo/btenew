<?php

abstract class Job
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');

        $this->queue = $this->di->get('queue');
        $this->redis = $this->di->get('redis');
    }

    protected function csvToArray($filename, $delimiter = ',', $header = null)
    {
        // read the CSV lines into a numerically indexed array
        $lines = @file($filename);
        if (!$lines) {
            return [];
        }

        $csv = array_map(function(&$line) use ($delimiter) {
            return str_getcsv($line, $delimiter);
        }, $lines);

        // use the first row's values as keys for all other rows if header not specified
        $columns = $header ? $header : $csv[0];

        array_walk($csv, function(&$a) use ($columns) {
            $a = array_combine($columns, $a);
        });

        if (!$header) {
            array_shift($csv); // remove column header row
        }

        return $csv;
    }

    protected function log($line)
    {
        static $first = true;

        $filename = $this->getLogFilename();

        echo $line, "\n";

        if ($first) {
            $first = false;
            error_log("\n", 3, $filename);
        }

        $line = date('H:i:s '). $line ."\n";

        error_log($line, 3, $filename);
    }

    protected function error($line)
    {
        static $first = true;

        $filename = $this->getErrorLogFilename();

        echo 'ERROR: ', $line, "\n";

        if ($first) {
            $first = false;
            error_log("\n", 3, $filename);
        }

        $line = date('H:i:s '). $line ."\n";

        error_log($line, 3, $filename);
    }

    // batch insert, MySql Syntax
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

        return $query . implode(",\n", $values) . "\nON DUPLICATE KEY UPDATE " . $update . ';';
    }

    /**
     * single insert, MS-SQL syntax
     *
     *  $data = [
     *      'Work Date'     => $workDate,
     *      'Channel'       => $channel,
     *      'Order #'       => $orderId,
     *      'Express'       => $xpress,
     *      'Stock Status'  => $stockStatus,
     *      'Qty'           => $qty,
     *      'Supplier'      => $supplier,
     *      'Supplier SKU'  => $sku,
     *      'Mfr #'         => $mfrpn,
     *      'Supplier #'    => $ponum,
     *      'Remarks'       => '',
     *      'Xpress'        => $xpress,
     *      'RelatedSKU'    => '',
     *      'Dimension'     => '',
     *  ];
     *
     *  $sql = $this->insertMssql('BestbuyCA', $data);
     */
    protected function insertMssql($table, $data)
    {
        $columns = '[' . implode('], [', array_keys($data)) . ']';

        $query = "INSERT INTO [$table] ($columns) VALUES\n";

        foreach($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = "'" .str_replace("'", "''", $val). "'";
            }
            if (is_null($val)) {
                $data[$key] = 'NULL';
            }
        }

        $values = implode(', ', $data);

        return "$query ($values)";
    }

    /**
     * This method can be overrided by derived class to return different filename
     */
    protected function getLogFilename()
    {
        $today = date('Y-m-d');
        $filename = APP_DIR . "/logs/job-$today.log";

        return $filename;
    }

    /**
     * This method can be overrided by derived class to return different filename
     */
    protected function getErrorLogFilename()
    {
        $today = date('Y-m-d');
        $filename = APP_DIR . "/logs/error-$today.log";

        return $filename;
    }

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }

    protected function getMasterSku($sku)
    {
        $skuService = $this->di->get('skuService');
        return $skuService->getMasterSku($sku);
    }
}
