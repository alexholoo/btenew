<?php

abstract class Job
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1');
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

        $line = date('Y-m-d H:i:s '). $line ."\n";

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

    protected function getLogFilename()
    {
        $today = date('Y-m-d');
        $filename = APP_DIR . "/logs/job-$today.log";

        return $filename;
    }

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
}
