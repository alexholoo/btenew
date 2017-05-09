<?php

namespace Service;

use Phalcon\Di\Injectable;

class OverstockService extends Injectable
{
    public function get($sku)
    {
        $accdb = $this->openAccessDB();

        $sql = "SELECT * FROM [overstock] WHERE [SKU Number] ='$sku'";

        $result = $accdb->query($sql)->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            $result['sku']    = $result['SKU Number'];
            $result['qty']    = $result['Actual Quantity'];
            $result['UPC']    = $result['UPC Code'];
            $result['weight'] = $result['Weight(lbs)'];
        }
        return $result;
    }

    public function isOverstocked($sku)
    {
        $result = $this->get($sku);
        return (boolean)$result;
    }

    public function getAvail($sku)
    {
        $result = $this->get($sku);

        $qty = 0;

        if ($result) {
            $qty = $result['Actual Quantity'];
        }

        return $qty;
    }

    /**
     * for restore
     */
    public function import($filename)
    {
        if (!($fp = @fopen($filename, 'rb'))) {
            // "Failed to open file: $filename\n";
            return;
        }

        fgetcsv($fp); // skip the first line

        $this->db->execute('TRUNCATE TABLE overstock');

        $columns = $this->getColumns();

        $count = 0;

        while (($fields = fgetcsv($fp))) {

            if (count($columns) != count($fields)) {
                // Error:
                continue;
            }

            $data = array_combine($columns, $fields);

            try {
                $this->db->insertAsDict('overstock', $data);
                $count++;

            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }

        fclose($fp);

        return $count;
    }

    /**
     * for backup
     */
    public function export($filename)
    {
        if (!($fp = @fopen($filename, 'w'))) {
            // "Failed to create file: $filename\n";
            return;
        }

        fputcsv($fp, $this->getColumns());

        $result = $this->db->fetchAll('SELECT * FROM overstock');

        foreach($result as $row) {
            fputcsv($fp, [
                $row['sku'],
                $row['title'],
                $row['cost'],
                $row['condition'],
                $row['allocation'],
                $row['qty'],
                $row['mpn'],
                $row['note'],
                $row['upc'],
                $row['weight'],
                $row['reserved'],
                $row['row_num'],
            ]);
        }

        fclose($fp);
    }

    protected function getColumns()
    {
        return [
            'sku',
            'title',
            'cost',
            'condition',
            'allocation',
            'qty',
            'mpn',
            'note',
            'upc',
            'weight',
            'reserved',
            'row_num',
        ];
    }

    protected function openAccessDB()
    {
        if (!empty($this->accdb)) {
            return $this->accdb;
        }

        $dbname = "z:/BTE-Price-List/bte-dataprocess-files.accdb";

        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/bte-dataprocess-files.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $this->accdb = new \PDO($dsn);

        return $this->accdb;
    }
}
