<?php

class Ingram_Pricelist_Importer extends Pricelist_Importer
{
    public function run($argv = [])
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function import()
    {
        $filename = Filenames::get('ingram.pricelist');

        if (!file_exists($filename)) {
           #$this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $table = 'pricelist_ing';
        $this->db->execute("TRUNCATE TABLE $table");

        $columns = include('./config/pricelist-columns-ing.php');

        $items = [];

        while (($fields = fgetcsv($fp))) {
            $fields = array_map('trim', $fields);
            if (count($fields) != count($columns)) {
                continue;
            }

            $item = array_combine($columns, $fields);

            $item['sku']    = 'ING-' . $item['sku'];
            $item['length'] = floatval($item['length']);
            $item['width']  = floatval($item['width']);
            $item['height'] = floatval($item['height']);
            $item['weight'] = floatval($item['weight']);
            $item['a']      = floatval($item['a']); // TODO ??
            $item['upc']    = $item['upc'] == '9999999999999' ? null : $item['upc'];

            $item['retail_price']   = floatval($item['retail_price']);
            $item['customer_price'] = floatval($item['customer_price']);

            $items[] = array_values($item);

            if (count($items) == 1000) {
                try {
                    $sql = $this->genInsertSql($table, $columns, $items);
                    $this->db->execute($sql);
                    $items = [];
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
        }

        if (count($items)) {
            try {
                $sql = $this->genInsertSql($table, $columns, $items);
                $this->db->execute($sql);
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        fclose($fp);
    }
}
