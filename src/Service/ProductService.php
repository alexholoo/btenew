<?php

namespace Service;

use Phalcon\Di\Injectable;

class ProductService extends Injectable
{
    public function isBrandBlocked($name, $marketplace = '')
    {
        $sql = "SELECT * FROM blocked_brands WHERE brand='$name'";

        $result = $this->db->fetchOne($sql);

        if ($result) {
            unset($result['refnum']);
            unset($result['createdon']);

            if ($marketplace) {
                $array = array_change_key_case($result);
                $marketplace = strtolower($marketplace);
                return isset($result[$marketplace]) && $result[$marketplace];
            }
        }

        return $result;
    }

    public function isBlockedByAmazonCA($sku)
    {
        // TODO
        return false;
    }

    public function getMinAllowedPrice($sku)
    {
        // TODO
        return 0;
    }

    public function shouldIgnoreItem($sku)
    {
        // TODO
        return false;
    }

    public function getAsinFromSku($sku)
    {
        $sql = "SELECT asin FROM sku_asin_map WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['asin'] : '';
    }

    public function getAsin($sku)
    {
        $this->getAsinFromSku($sku);
    }

    public function getUpcFromSku($sku)
    {
        $sql = "SELECT upc FROM sku_upc_map WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['upc'] : '';
    }

    public function getUpc($sku)
    {
        $this->getUpcFromSku($sku);
    }

    public function getMpnFromSku($sku)
    {
        $sql = "SELECT mpn FROM sku_mpn_map WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['mpn'] : '';
    }

    public function getMpn($sku)
    {
        $this->getMpnFromSku($sku);
    }

    public function getSkuGroupFromAsin($asin) // getAsinGroup($asin)
    {
        $sql = "SELECT * FROM sku_asin_map WHERE asin='$asin'";
        $skuGroup = $this->db->fetchAll($sql);
        return array_column($skuGroup, 'sku');
    }

    public function getSkuGroupFromUpc($upc) // getUpcGroup($upc)
    {
        $sql = "SELECT sku FROM sku_upc_map WHERE upc='$upc'";
        $skuGroup = $this->db->fetchAll($sql);
        return array_column($skuGroup, 'sku');
    }

    public function getSkuGroupFromMpn($mpn) // getMpnGroup($mpn)
    {
        $sql = "SELECT sku FROM sku_mpn_map WHERE mpn='$mpn'";
        $skuGroup = $this->db->fetchAll($sql);
        return array_column($skuGroup, 'sku');
    }

    public function getSkuGroup($sku, $greedy = true)
    {
        $asin = $this->getAsinFromSku($sku);
        $upc  = $this->getUpcFromSku($sku);

        $skuGroup1 = $this->getSkuGroupFromAsin($asin);
        $skuGroup2 = $this->getSkuGroupFromUpc($upc);

        if ($greedy) {
            return array_unique(array_merge($skuGroup1, $skuGroup2));
        }

        return array_intersect($skuGroup1, $skuGroup2);
    }
}
