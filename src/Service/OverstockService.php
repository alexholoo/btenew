<?php

namespace Service;

use Phalcon\Di\Injectable;

/**
 * NOTE:
 *   OverstockService can only call from backend Job, it cannot
 *   call from frontend (in browser), because it needs php64
 */
class OverstockService extends Injectable
{
    public function load()
    {
        $sql = "SELECT * FROM overstock ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

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
     *  $info = [
     *      'sku'        => '',
     *      'title'      => '',
     *      'cost'       => '0',
     *      'condition'  => 'New',
     *      'allocation' => 'ALL',
     *      'qty'        => '0',
     *      'mpn'        => '',
     *      'note'       => '',
     *      'weight'     => '0',
     *      'upc'        => '',
     *  ];
     */
    public function add($info)
    {
        $accdb = $this->openAccessDB();
        $logger = $this->loggerService;

        $sku = $info['sku'];

        $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$sku'";
        $row = $accdb->query($sql)->fetch();

        if ($row) {
            $totalQty = $row['Actual Quantity'] + $info['qty'];
            $totalPrice = $row['Actual Quantity'] * $row['cost'] + $info['cost'] * $info['qty'];
            $newCost = round($totalPrice/$totalQty);

            $sql = "UPDATE [overstock] SET [Actual Quantity]=$totalQty, [cost]=$newCost"
                 . " WHERE [SKU Number]='$sku'";

            $ret = $accdb->exec($sql);

            if (!$ret && $accdb->errorCode() != '00000') {
                $logger->error(__METHOD__);
                $logger->error(print_r($accdb->errorInfo(), true));
                $logger->error($sql);
                return false;
            }
        } else {
            $sql = $this->insertMssql("overstock", [
                'SKU Number'      => $info['sku'],
                'Title'           => $info['title'],
                'cost'            => intval($info['cost']),
                'condition'       => $info['condition'],
                'Allocation'      => $info['allocation'],
                'Actual Quantity' => intval($info['qty']),
                'MPN'             => $info['mpn'],
                'note'            => $info['note'],
                'UPC Code'        => $info['upc'],
                'Weight(lbs)'     => floatval($info['weight']),
                'Reserved'        => '',
               #'ID'              => '',
            ]);

            $ret = $accdb->exec($sql);
            if (!$ret && $accdb->errorCode() != '00000') {
                $logger->error(__METHOD__);
                $logger->error(print_r($accdb->errorInfo(), true));
                $logger->error($sql);
                return false;
            }
        }

        $this->saveLog($info);
    }

    /**
     * @param array $info
     * @see this->add()
     */
    protected function saveLog($info)
    {
        /*
        $accdb = $this->openAccessDB();

        $sql = $this->insertMssql("overstock-log", [
            'sku'        => $info['sku'],
            'title'      => $info['title'],
            'cost'       => intval($info['cost']),
            'condition'  => $info['condition'],
            'allocation' => $info['allocation'],
            'qty'        => intval($info['qty']),
            'mpn'        => $info['mpn'],
            'note'       => $info['note'],
            'upc'        => $info['upc'],
            'weight'     => floatval($info['weight']),
        ]);

        $ret = $accdb->exec($sql);

        if (!$ret && $accdb->errorCode() != '00000') {
            $logger = $this->loggerService;
            $logger->error(__METHOD__);
            $logger->error(print_r($accdb->errorInfo(), true));
            $logger->error($sql);
        }
        */

        $this->db->insertAsDict("overstock_log", [
            'sku'        => $info['sku'],
            'title'      => $info['title'],
            'cost'       => $info['cost'],
            'condition'  => $info['condition'],
            'allocation' => $info['allocation'],
            'qty'        => $info['qty'],
            'mpn'        => $info['mpn'],
           #'note'       => $info['note'],
            'upc'        => $info['upc'],
            'weight'     => $info['weight'],
        ]);
    }

    public function loadHistory()
    {
        $sql = "SELECT * FROM overstock_log ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
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
}
