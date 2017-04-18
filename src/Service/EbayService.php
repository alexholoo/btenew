<?php

namespace Service;

use Phalcon\Di\Injectable;

class EbayService extends Injectable
{
    public function findSku($sku, $site)
    {
        $table = ($site == 'GFS') ? 'ebay_gfs_listing' : 'ebay_odo_listing';

        $sql = "SELECT * FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }
}
