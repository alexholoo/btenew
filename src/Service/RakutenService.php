<?php

namespace Service;

use Phalcon\Di\Injectable;

class RakutenService extends Injectable
{
    public function findSku($sku, $site = 'US')
    {
        $sql = "SELECT * FROM rakuten_us_listing WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }
}
