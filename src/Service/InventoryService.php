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

    public function add($info)
    {
    }

    public function deduct($sku, $order)
    {
    }

    public function loadChanges()
    {
        $sql = "SELECT * FROM bte_inventory_change ORDER BY id DESC";
        $result = $this->db->fetchAll($sql);
        return $result;
    }
}
