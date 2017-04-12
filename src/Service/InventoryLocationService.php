<?php

namespace Service;

use Phalcon\Di\Injectable;

class InventoryLocationService extends Injectable
{
    public function get($id)
    {
        $sql = "SELECT * FROM inventory_location WHERE id=$id";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    public function search($keyword, $searchby)
    {
        // ORM doesn't help for the special queries
        if ($searchby == 'partnum') {
            if ($this->skuService->isSku($keyword)) {
                if ($mpn = $this->skuService->getMpn($keyword)) {
                    $keyword = $mpn;
                }
            }
            $sql = 'SELECT * FROM inventory_location WHERE partnum LIKE ? LIMIT 20';
            $result = $this->db->query($sql, array("%$keyword%"));
        } elseif ($searchby == 'upc') {
            $sql = 'SELECT * FROM inventory_location WHERE upc LIKE ? LIMIT 20';
            $result = $this->db->query($sql, array("%$keyword"));
        } elseif ($searchby == 'location') {
            $sql = "SELECT * FROM inventory_location WHERE location = ? LIMIT 20";
            $result = $this->db->query($sql, array($keyword));
        } elseif ($searchby == 'note') {
            $sql = "SELECT * FROM inventory_location WHERE note LIKE ? LIMIT 20";
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

    public function add($data)
    {
        // TODO: who is doing this? add a new column(userid) to table.
        try {
            $this->db->insertAsDict('inventory_location',
                array(
                    'partnum'  => $data['partnum'],
                    'upc'      => $data['upc'],
                    'location' => $data['location'],
                    'qty'      => $data['qty'],
                    'sn'       => $data['sn'],
                    'note'     => $data['note'],
                )
            );
        } catch (\Exception $e) {
            // echo $e->getMessage(), EOL;
            return false;
        }

        return $this->db->lastInsertId();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM inventory_location WHERE id=$id";
        $this->db->execute($sql);
        return $this->db->affectedRows();
    }
}
