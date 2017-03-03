<?php

class DH_Pricelist extends PricelistImporter
{
    public function import()
    {
        $filename = Filenames::get('dh.pricelist');

        if (!file_exists($filename)) {
           #$this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $table = 'pricelist_dh';
        $this->db->execute("TRUNCATE TABLE $table");

        $columns = include('./config/pricelist-columns-dh.php');

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
