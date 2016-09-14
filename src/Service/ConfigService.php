<?php

namespace Service;

use Utility\Arr;
use Phalcon\Di\Injectable;

class ConfigService extends Injectable
{
    protected $config = [];

    protected function loadConfig()
    {
        if ($this->config) {
            return $this->config;
        }

        $sql = "SELECT * FROM config";
        $result = $this->db->query($sql);

        while ($row = $result->fetch()) {
            $supplier = $row['supplier'];
            $section = $row['section'];
            $name = $row['name'];
            $value = $row['value'];

            // synnex.xmlapi.username
            $this->config[$supplier][$section][$name] = $value;

            // xmlapi.synnex.username
            // $this->config[$section][$supplier][$name] = $value;

            // this is an bad idea, get('dh.xmlapi') won't work
            // $this->config["$section.$supplier.$name"] = $value;
        }

        return $this->config;
    }

    /**
     * usage:
     *
     *   $val = $this->ConfigService->get('dh.xmlapi.username');
     *   $cfg = $this->ConfigService->get('dh.xmlapi');
     *   $cfg = $this->ConfigService->get('dh');
     *
     * @param  string $key
     * @return string
     */
    public function get($key)
    {
        $this->loadConfig();
        return Arr::get($this->config, $key);
    }

    /**
     * usage:
     *
     *   $this->ConfigService->set('dh.xmlapi.username', 'newvalue');
     *
     * @param  string $key
     * @param  string $value
     */
    public function set($key, $value)
    {
        $parts = explode('.', $key);

        // $key must look like: supplier.section.name
        if (count($parts) != 3) {
            return;
        }

        $supplier = $parts[0];
        $section = $parts[1];
        $name = $parts[2];

        $sql = "UPDATE config SET value=? WHERE supplier=? AND section=? AND name=?";
        $this->db->execute($sql, array($value, $supplier, $section, $name));

        if ($this->db->affectedRows() == 0) {
            try {
                $this->db->insertAsDict('config', [
                    'supplier' => $supplier,
                    'section'  => $section,
                    'name'     => $name,
                    'value'    => $value,
                    'desc'     => '',
                ]);
            } catch (\Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }

        // force to reload config from database
        $this->config = [];
    }
}
