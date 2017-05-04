<?php

namespace Service;

use Phalcon\Di\Injectable;

class RmaService extends Injectable
{
    public function getRecords()
    {
        $sql = "SELECT * FROM rma_records ORDER BY date_in DESC LIMIT 20";
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }
}
