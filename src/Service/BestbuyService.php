<?php

namespace Service;

use Toolkit\Str;
use Phalcon\Di\Injectable;

class BestbuyService extends Injectable
{
    public function findSku($sku, $site = 'CA')
    {
        $sql = "SELECT * FROM bestbuy_ca_listing WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    public function getCategory($sku)
    {
        static $catmap = null;

        if (!$catmap) {
            $catmap = $this->db->fetchAll('SELECT * FROM keyword_bestbuy_category');
            $catmap = array_column($catmap, 'category', 'keyword');
        }

        $name = $this->skuService->getName($sku);

        foreach ($catmap as $kwd => $category) {
            if (Str::matchAll($kwd, $name)) {
                return $category;
            }
        }

        return '';
    }
}
