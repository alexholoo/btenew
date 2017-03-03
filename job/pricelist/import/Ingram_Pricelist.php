<?php

class Ingram_Pricelist extends PricelistImporter
{
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

            $item['sku'] = 'ING-' . $item['sku'];

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
