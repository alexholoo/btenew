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
        $names = array(
            'SKU',
            'recommended_pn',
            'syn_pn', 'syn_cost', 'syn_qty',
            'td_pn',  'td_cost',  'td_qty',
            'ing_pn', 'ing_cost', 'ing_qty',
            'dh_pn',  'dh_cost',  'dh_qty',
            'asi_pn', 'asi_cost', 'asi_qty',
            'tak_pn', 'tak_cost', 'tak_qty',
            'ep_pn',  'ep_cost',  'ep_qty',
            'BTE_PN', 'BTE_cost', 'BTE_qty',
            'Manufacturer',
            'UPC',
            'MPN',
            'MAP_USD',
            'MAP_CAD',
            'Width', 'Length', 'Depth',
            'Weight',
            'ca_ebay_blocked',
            'us_ebay_blocked',
            'ca_newegg_blocked',
            'us_newegg_blocked',
            'us_amazon_blocked',
            'ca_amazon_blocked',
            'uk_amazon_blocked',
            'jp_amazon_blocked',
            'mx_amazon_blocked',
            'note',
            'name',
            'best_cost',
            'overall_qty'
        );

        $value = json_decode($this->redis->get($sku));

        if (count($value) == count($names)) {
            return array_combine($names, $value);
        }

        return $value;
    }
}
