<?php

namespace Service;

use Phalcon\Di\Injectable;

class EbayService extends Injectable
{
    public function findSku($sku, $site)
    {
        $table = ($site == 'GFS') ? 'ebay_gfs_listings' : 'ebay_odo_listings';

        $sql = "SELECT * FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }
}
