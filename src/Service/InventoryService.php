<?php

namespace Service;

use Phalcon\Di\Injectable;

class InventoryService extends Injectable
{
    public function load()
    {
        $sql = "SELECT * FROM bte_inventory ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function get($partnum)
    {
        $sql = "SELECT * FROM bte_inventory WHERE partnum='$partnum'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    public function getAvail($partnum)
    {
        $result = $this->get($partnum);

        $qty = 0;

        if ($result) {
            $qty = $result['qty'];
        }

        return $qty;
    }

    public function search($kwd)
    {
        if (strlen($kwd) == 0) {
            return $this->load();
        }
        $sql = "SELECT * FROM bte_inventory WHERE partnum LIKE '%$kwd%' LIMIT 20";
        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function add($info)
    {
        $this->db->insertAsDict('bte_inventory', $info);
    }

    public function update($id, $info)
    {
        $this->db->updateAsDict('bte_inventory', $info, "id=$id");
    }

    public function deduct($order)
    {
        $sku = $order['sku'];
        $qty = $order['qty'];

        $row = $this->get($sku);

        if (!$row || strtolower(trim($row['type'])) != 'self') {
            return;
        }

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
            'qty' => $remaining,
        ];

        if ($remaining == 0) {
            // Mark the item as 'out of stock' by prefixing *** the part number
            $updateFields['partnum'] = "***$sku";
        }

        $this->db->updateAsDict('bte_inventory', $updateFields, "partnum='$sku'");

        // log the change
        $row['order_date'] = $order['date'];
        $row['channel']    = $order['channel'];
        $row['order_id']   = $order['order_id'];
        $row['change']     = $change;
        $row['qty']        = $remaining;

        unset($row['id']);
        unset($row['createdon']);
        unset($row['updatedon']);

        $this->db->insertAsDict("bte_inventory_change", $row);

        return $remaining;
    }

    public function loadChanges($id)
    {
        $sql = "SELECT * FROM bte_inventory_change ORDER BY id DESC";

        if ($id) {
            $row = $this->db->fetchOne("SELECT * FROM bte_inventory WHERE id=$id");
            if ($row) {
                $partnum = trim($row['partnum'], '*');
                $sql = "SELECT * FROM bte_inventory_change WHERE partnum='$partnum' ORDER BY id DESC";
            }
        }

        $result = $this->db->fetchAll($sql);
        return $result;
    }

    public function searchChanges($kwd)
    {
        if (strlen($kwd) == 0) {
            return $this->loadChanges('');
        }
        $sql = "SELECT * FROM bte_inventory_change WHERE partnum LIKE '%$kwd%' ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }
}
