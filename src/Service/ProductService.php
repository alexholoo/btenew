<?php

namespace Service;

use Phalcon\Di\Injectable;

// TODO: most code should move to SkuService

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

    public function getAsinFromSku($sku, $market = 'CA')
    {
        $amazonService = $this->di->get('amazonService');
        return $amazonService->getAsinFromSku($sku, $market);

        // this is not correct, because ASIN are different in US/CA
        // $sql = "SELECT asin FROM sku_asin_map WHERE sku='$sku'";
        // $result = $this->db->fetchOne($sql);
        // return $result ? $result['asin'] : '';
    }

    public function getAsin($sku, $market = 'CA')
    {
        return $this->getAsinFromSku($sku, $market);
    }

    public function getUpcFromSku($sku)
    {
        $sql = "SELECT upc FROM sku_upc_map WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['upc'] : '';
    }

    public function getUpc($sku)
    {
        return $this->getUpcFromSku($sku);
    }

    public function getMpnFromSku($sku)
    {
        $sql = "SELECT mpn FROM sku_mpn_map WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['mpn'] : '';
    }

    public function getMpn($sku)
    {
        return $this->getMpnFromSku($sku);
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

    public function getSellingPrice($sku, $market = 'CA')
    {
        $amazonService = $this->di->get('amazonService');
        return $amazonService->getSellingPrice($sku, $market);
    }

    public function getImageUrl($sku, $size = 'L')
    {
        if (substr($sku, 0, 3) == 'DH-') {
            $sku = substr($sku, 3);
            return "https://www.dandh.ca/images/prod300/$sku.jpg"; // TODO
        }

        $amazonService = $this->di->get('amazonService');

        $asin = $this->getAsin($sku, 'CA');
        if ($asin) {
            return $amazonService->getImageUrl($asin, $size);
        }

        $asin = $this->getAsin($sku, 'US');
        if ($asin) {
            return $amazonService->getImageUrl($asin, $size);
        }

        return '';
    }

    public function getMasterSku($sku)
    {
        // This is not always correct, DON'T call it
        $sql = "SELECT * FROM master_sku_list WHERE sku='$sku'";
        $info = $this->db->fetchOne($sql);
        return $info;
    }
}
