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
        $sql = "SELECT * FROM overstock WHERE sku='$sku'";
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

    public function search($kwd)
    {
        $sql = "SELECT * FROM overstock WHERE sku LIKE '%$kwd%' LIMIT 20";
        $result = $this->db->fetchAll($sql);
        return $result;
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

    public function deduct($sku, $order)
    {
        $row = $this->get($sku);
        if (!$row) {
            return false;
        }

        $qty = $order['qty'];
        $qtyOnHand = $row['qty'];

        $remaining = 0;
        $change = 'No change';
        if ($qtyOnHand > 0) {
            $change = "-$qty";
            $remaining = $qtyOnHand - $qty;
            if ($remaining < 0) {
                $remaining = 0;
                $change = "-$qtyOnHand oversold";
            }
        }

        $updateFields = [
            'qty'      => $remaining,
            'reserved' => '',
        ];

        // Mark the item as 'out of stock' by prefixing *** the part number
        if ($remaining == 0) {
            $today = date('Y-m-d');
            $updateFields['sku'] = "***$sku";
            $updateFields['reserved'] = "Sold out on $today";
        }

        $this->db->updateAsDict('overstock', $updateFields, "sku='$sku'");

        // log the deduction
        $row['order_date'] = $order['date'];
        $row['channel']    = $order['channel'];
        $row['order_id']   = $order['order_id'];
        $row['change']     = $change;
        $row['sku']        = $sku;
        $row['qty']        = $remaining;
        $row['reserved']   = '';

        unset($row['id']);
        unset($row['createdon']);
        unset($row['updatedon']);

        $this->db->insertAsDict('overstock_change', $row);
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

    public function loadLogs()
    {
        $sql = "SELECT * FROM overstock_log ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function loadChanges($id)
    {
        $sql = "SELECT * FROM overstock_change ORDER BY id DESC";
        if ($id) {
            $row = $this->db->fetchOne("SELECT * FROM overstock WHERE id=$id");
            if ($row) {
                $sku = trim($row['sku'], '*');
                $sql = "SELECT * FROM overstock_change WHERE sku='$sku' ORDER BY id DESC";
            }
        }
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function newStock($partnum, $upc, $qty, $location)
    {
        $info = false;

        if ($partnum) {
            $info = $this->skuService->getMasterSku($partnum);
        }

        if (!$info && $upc) {
            $info = $this->skuService->getMasterSku($upc);
        }

        if (!$info) {
            return false;
        }

        $sku = $info['recommended_pn'];
        $mfr = $info['MFR'];

        $altSkus = $this->skuService->getAltSkus($sku);

        $this->add([
            'sku'        => $sku,
            'title'      => $mfr. ' ' .$info['name'],
            'cost'       => round($info['best_cost']),
            'condition'  => 'New',
            'allocation' => 'ALL',
            'qty'        => $qty,
            'mpn'        => $info['MPN'],
            'note'       => implode('; ', $altSkus),
            'weight'     => $info['Weight'],
            'upc'        => $info['UPC'],
        ]);

        $this->inventoryLocationService->add([
            'partnum'  => $partnum ?? $info['MPN'],
            'upc'      => $upc ?? $info['UPC'],
            'location' => $location,
            'qty'      => $qty,
            'sn'       => '',
            'note'     => '',
        ]);

        return true;
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
