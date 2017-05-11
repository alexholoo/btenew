<?php

namespace Service;

use Phalcon\Di\Injectable;

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
        $sql = "SELECT * FROM overstock WHERE sku ='$sku'";
        $result = $this->db->fetchOne($sql);
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
            $qty = $result['qty'];
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
        $logger = $this->loggerService;

        $sku = $info['sku'];

        $row = $this->get($sku);

        if ($row) {
            $totalQty = $row['qty'] + $info['qty'];
            $totalPrice = $row['qty'] * $row['cost'] + $info['cost'] * $info['qty'];
            $newCost = round($totalPrice/$totalQty);

            $this->db->updateAsDict("overstock",
                [
                    'qty'  => $totalQty,
                    'cost' => $newCost,
                ],
                "sku='$sku'"
            );
        } else {
            $this->db->insertAsDict("overstock", [
                'sku'        => $info['sku'],
                'title'      => $info['title'],
                'cost'       => $info['cost'],
                'condition'  => $info['condition'],
                'allocation' => $info['allocation'],
                'qty'        => $info['qty'],
                'mpn'        => $info['mpn'],
                'note'       => $info['note'],
                'upc'        => $info['upc'],
                'Weight'     => $info['weight'],
                'reserved'   => '',
            ]);
        }

        $this->saveLog($info);
    }

    /**
     * @param array $info
     * @see this->add()
     */
    protected function saveLog($info)
    {
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

    public function update($id, $info)
    {
        $this->db->updateAsDict("overstock", $info, "id=$id");
    }

    public function loadHistory()
    {
        $sql = "SELECT * FROM overstock_log ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function loadChange()
    {
        $sql = "SELECT * FROM overstock_change ORDER BY id DESC";
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
                $row['createdon'],
                $row['updatedon'],
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
            'createdon',
            'updatedon',
        ];
    }
}
