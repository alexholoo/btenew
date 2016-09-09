<?php

namespace Service;

use Phalcon\Di\Injectable;

class ConfigService extends Injectable
{
    public function loadConfig()
    {
        static $config = [];

        if ($config) {
            return $config;
        }

        $sql = "SELECT * FROM config";
        $result = $this->db->query($sql);

        while ($row = $result->fetch()) {
            $supplier = $row['supplier'];
            $section = $row['section'];
            $name = $row['name'];
            $value = $row['value'];

            $config[$section][$supplier][$name] = $value;
        }

        return $config;
    }
}
