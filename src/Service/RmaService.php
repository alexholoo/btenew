<?php

namespace Service;

use Phalcon\Di\Injectable;

class RmaService extends Injectable
{
    public function getRecords()
    {
        $sql = "SELECT * FROM rma_records ORDER BY date_in DESC";
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }

    public function getDetail($id)
    {
        $sql = "SELECT * FROM rma_records WHERE id=$id";
        $row = $this->db->fetchOne($sql);
        return $row;
    }

    public function update($id, $info)
    {
        $this->db->updateAsDict('rma_records', $info, "id = $id");
    }

    public function add($info)
    {
        $this->db->insertAsDict('rma_records', [
            'date_in'         => date('Y-m-d'),
            'product_desc'    => $info['product_desc'],
            'partnum'         => $info['partnum'],
            'our_recnum'      => $info['our_recnum'],
            'status'          => $info['status'],
            'supplier'        => $info['supplier'],
            'order_id'        => $info['order_id'],
            'after_checked'   => $info['after_checked'],
            'date_shipout'    => '',
            'ship_method'     => '',
            'supplier_recnum' => '',
            'done'            => '',
        ]);
    }
}
