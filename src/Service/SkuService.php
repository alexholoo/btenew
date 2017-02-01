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
}
