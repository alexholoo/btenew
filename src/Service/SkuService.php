<?php

namespace Service;

use Phalcon\Di\Injectable;

class SkuService extends Injectable
{
    public function getMasterSku($sku)
    {
        static $names = array(
            'SKU',
            'recommended_pn',
            'syn_pn', 'syn_cost', 'syn_qty',
            'td_pn',  'td_cost',  'td_qty',
            'ing_pn', 'ing_cost', 'ing_qty',
            'dh_pn',  'dh_cost',  'dh_qty',
            'asi_pn', 'asi_cost', 'asi_qty',
            'tak_pn', 'tak_cost', 'tak_qty',
            'ep_pn',  'ep_cost',  'ep_qty',
            'BTE_PN', 'BTE_cost', 'BTE_qty',
            'Manufacturer',
            'UPC',
            'MPN',
            'MAP_USD',
            'MAP_CAD',
            'Width', 'Length', 'Depth',
            'Weight',
            'ca_ebay_blocked',
            'us_ebay_blocked',
            'ca_newegg_blocked',
            'us_newegg_blocked',
            'us_amazon_blocked',
            'ca_amazon_blocked',
            'uk_amazon_blocked',
            'jp_amazon_blocked',
            'mx_amazon_blocked',
            'note',
            'name',
            'best_cost',
            'overall_qty'
        );

        $value = json_decode($this->redis->get($sku));

        if (count($value) == count($names)) {
            return array_combine($names, $value);
        }

        return $value;

        // this is not always correct
        // $sql = "SELECT * FROM master_sku_list WHERE sku='$sku'";
        // $info = $this->db->fetchOne($sql);
        // return $info;
    }

    public function getAltSkus($sku)
    {
        $info = $this->getMasterSku($sku);

        return array_filter([
            $info['syn_pn'],
            $info['td_pn'],
            $info['ing_pn'],
            $info['dh_pn'],
            $info['asi_pn'],
            $info['tak_pn'],
            $info['ep_pn'],
        ]);
    }

    public function getUpc($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['UPC']) ? $info['UPC'] : '';
    }

    public function getMpn($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['MPN']) ? $info['MPN'] : '';
    }

    public function getWeight($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['Weight']) ? $info['Weight'] : '';
    }

    public function getMfr($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['Manufacturer']) ? $info['Manufacturer'] : '';
    }

    public function getName($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['name']) ? $info['name'] : '';
    }

    public function getBestCost($sku)
    {
        $info = $this->getMasterSku($sku);
        return isset($info['best_cost']) ? $info['best_cost'] : '';
    }

    public function getAsin($sku, $market = 'CA')
    {
        $amazonService = $this->di->get('amazonService');
        return $amazonService->getAsinFromSku($sku, $market);

        // this is not correct, because ASIN are different in US/CA
        // $sql = "SELECT asin FROM sku_asin_map WHERE sku='$sku'";
        // $result = $this->db->fetchOne($sql);
        // return $result ? $result['asin'] : '';
    }

    public function isBlocked($sku, $market)
    {
        // 'ca_ebay',
        // 'us_ebay',
        // 'ca_newegg',
        // 'us_newegg',
        // 'us_amazon',
        // 'ca_amazon',
        // 'uk_amazon',
        // 'jp_amazon',
        // 'mx_amazon',

        $info = $this->getMasterSku($sku);

        $blocked = strtolower($market)."_blocked";
        return isset($info[$blocked]) ? $info[$blocked] : '';
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

    public function getMinAllowedPrice($sku, $currency = 'CAD')
    {
        // 'MAP_USD'/'MAP_CAD',
        $info = $this->getMasterSku($sku);

        $MAP = "MAP_$currency";
        return isset($info[$MAP]) ? $info[$MAP] : '';
    }

    public function shouldIgnoreItem($sku) { }
}
