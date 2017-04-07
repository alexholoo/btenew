<?php

abstract class Job
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');

        $this->queue = $this->di->get('queue');
        $this->redis = $this->di->get('redis');

        $this->errlog = $this->di->get('loggerService');
        $this->joblog = $this->di->get('loggerService');
        $this->joblog->setFilename('job.log');
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
        echo $line, "\n";
        $this->joblog->info($line);
    }

    protected function error($line)
    {
        echo 'ERROR: ', $line, "\n";
        $this->errlog->error($line);
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

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }

    protected function getMasterSku($sku)
    {
        $skuService = $this->di->get('skuService');
        return $skuService->getMasterSku($sku);
    }

    protected function getJob($filename)
    {
        // load base class if exists
        $dir = dirname($filename);
        $base = "$dir/Base.php";
        if (file_exists($base)) {
            include_once($base);
        }

        // load the job class
        include_once($filename);

        // get class name from filename
        $class = pathinfo($filename, PATHINFO_FILENAME);

        // create job object
        $job = false;
        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }

    protected function getJobs($folder)
    {
        $jobs = [];

        $folder = trim($folder, '/');
        foreach (glob("$folder/*.php") as $filename) {
            $job = $this->getJob($filename);
            if ($job) {
                $jobs[] = $job;
            }
        }

        return $jobs;
    }
}
