<?php

class Synnex_Pricelist extends PricelistImporter
{
    public function import()
    {
        $filename = Filenames::get('synnex.pricelist');

        if (!file_exists($filename)) {
           #$this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');
        fgets($fp); // skip first line

        $start = microtime(true);

        $table = 'pricelist_syn';
        $this->db->execute("TRUNCATE TABLE $table");

        $columns = include('./config/pricelist-columns-syn.php');

        $items = [];
        while (($fields = fgetcsv($fp, 0, '~'))) {
            if (count($fields) != count($columns)) {
                $this->error('Incorrect number of columns: '. $fields[2]);
                continue;
            }

            $item = array_combine($columns, $fields);

            if ($item['qty'] == 0) {
                //continue;
            }

            $item['sku'] = 'SYN-' . $item['sku'];

            $items[] = array_values($item);

            if (count($items) == 1000) { // 2000 causes an error: MySQL server has gone away
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

        $duration = $this->elapsed($start);
        $this->log("$duration seconds elapsed");
    }
}
