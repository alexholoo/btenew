<?php

namespace Service;

use Phalcon\Di\Injectable;

class InventoryService extends Injectable
{
    public function searchLocation($keyword, $searchby)
    {
        // ORM doesn't help for the special queries
        if ($searchby == 'partnum') {
            $sql = 'SELECT * FROM inventory_location WHERE partnum LIKE ?';
            $result = $this->db->query($sql, array("%$keyword%"));
        } elseif ($searchby == 'upc') {
            $sql = 'SELECT * FROM inventory_location WHERE upc LIKE ?';
            $result = $this->db->query($sql, array("%$keyword"));
        } elseif ($searchby == 'location') {
            $sql = "SELECT * FROM inventory_location WHERE location = ?";
            $result = $this->db->query($sql, array($keyword));
        } elseif ($searchby == 'note') {
            $sql = "SELECT * FROM inventory_location WHERE note LIKE ?";
            $result = $this->db->query($sql, array("%$keyword%"));
        } else {
            return false;
        }

        $data = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
           $data[] = $row;
        }

        return $data;
    }

    public function addToLocation($data)
    {
        // TODO: who is doing this? add a new column(userid) to table.
        $this->db->insertAsDict('inventory_location',
            array(
                'partnum'  => $data['partnum'],
                'upc'      => $data['upc'],
                'location' => $data['location'],
                'qty'      => $data['qty'],
                'sn'       => '',
                'note'     => '',
            )
        );

        return true;
    }

    protected function isSku($keyword)
    {
        $parts = explode('-', $keyword);
        $sku = strtoupper($parts[0]);

        return in_array($sku, [ 'AS', 'BTE', 'ODO', 'SYN', 'ING', 'EP', 'TD', 'TAK', 'SP']);
    }

    protected function getMpn($sku)
    {
        return $this->skuService->getMpn($sku);
    }
}
