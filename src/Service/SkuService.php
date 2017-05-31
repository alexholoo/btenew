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
            'MFR',
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
            $info = array_combine($names, $value);
            $info['recommended_pn'] = str_replace('overstock ', '', $info['recommended_pn']);
            return $info;
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

        $skus = array_filter([
            $info['syn_pn'],
            $info['td_pn'],
            $info['ing_pn'],
            $info['dh_pn'],
            $info['asi_pn'],
            $info['tak_pn'],
            $info['ep_pn'],
            $info['BTE_PN'],
        ]);

        if (count($skus) > 1) {
            return $skus;
        }

        $upc = $this->getUpc($sku);
        if ($upc) {
            $list = $this->getSkuListByUPC($upc);
            $skus = array_merge($skus, $list);
        }

        return array_unique($skus);
    }

    public function getUpc($sku)
    {
        // First, try to get UPC from master_sku_list in Redis
        $info = $this->getMasterSku($sku);

        if (isset($info['UPC']) && $this->isUPC($info['UPC'])) {
            return $info['UPC'];
        }

        // If not found, try to get UPC from database
        $sql = "SELECT * FROM sku_upc_map WHERE sku='$sku'";
        $row = $this->db->fetchOne($sql);
        if ($row && $this->isUPC($row['upc'])) {
            return $row['upc'];
        }

        return false;
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

        $mfr = isset($info['MFR']) ? $info['MFR'] : '';

        if (!$mfr) {
            $mfr = $this->getBrand($sku);
        }

        return $mfr;
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
        # Amazon has better image quality than DH
        #if (substr($sku, 0, 3) == 'DH-') {
        #    $sku = substr($sku, 3);
        #    return "https://www.dandh.ca/images/prod300/$sku.jpg"; // TODO
        #}

        $amazonService = $this->di->get('amazonService');
        return $amazonService->getImageUrl($sku, $size);
    }

    public function getDescription($sku)
    {
        $amazonService = $this->di->get('amazonService');
        return $amazonService->getDescription($sku);
    }

    public function getMinAllowedPrice($sku, $currency = 'CAD')
    {
        // 'MAP_USD'/'MAP_CAD',
        $info = $this->getMasterSku($sku);

        $MAP = "MAP_$currency";
        return isset($info[$MAP]) ? $info[$MAP] : '';
    }

    public function shouldIgnoreItem($sku) { }

    protected function getBrand($sku)
    {
        static $brands = null;

        if (!$brands) {
            $brands = $this->db->fetchAll('SELECT * FROM keyword_brand');
            $brands = array_column($brands, 'brand', 'keyword');
        }

        $name = $this->getName($sku);
        foreach ($brands as $kwd => $brand) {
            if (stripos($name, $kwd) !== false) {
                return $brand;
            }
        }

        return '';
    }

    public function getSkuListByUPC($upc)
    {
        $result = $this->db->fetchAll("SELECT sku FROM sku_upc_map WHERE upc='$upc'");
        return $result ? array_column($result, 'sku') : [];
    }

    public function getSkuListByMPN($mpn)
    {
        $result = $this->db->fetchAll("SELECT sku FROM sku_mpn_map WHERE mpn='$mpn'");
        return $result ? array_column($result, 'sku') : [];
    }

    public function getSupplier($sku)
    {
        $names = [
            'AS'  => 'ASI',
            'SYN' => 'Synnex',
            'ING' => 'Ingram Micro',
            'EP'  => 'Eprom',
            'TD'  => 'Techdata',
            'TAK' => 'Taknology',
            'SP'  => 'Supercom',
        ];

        $parts = explode('-', $sku);
        $prefix = $parts[0];

        return isset($names[$prefix]) ? $names[$prefix] : $prefix;
    }

    public function isSku($str)
    {
        $parts = explode('-', $str);
        $sku = strtoupper($parts[0]);

        return count($parts) > 1 &&
               in_array($sku, ['AS', 'BTE', 'ODO', 'SYN', 'ING', 'EP', 'TD', 'TAK', 'SP']);
    }

    public function isUPC($str)
    {
        return preg_match('/^\d{12,13}$/', $str);
    }
}
