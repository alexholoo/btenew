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
}
