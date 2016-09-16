<?php

namespace Service;

use Phalcon\Di\Injectable;

class PricelistService extends Injectable
{
    protected function getSupplier($sku)
    {
        $parts = explode('-', $sku);
        $supplier = strtolower($parts[0]);

        return $supplier;
    }

    public function getPrice($sku)
    {
        $supplier = $this->getSupplier($sku);

        $priceFieldMap = [
            'dh'  => 'cost',
            'ing' => 'customer_price',
        ];

        $priceField = 'price';
        if (isset($priceFieldMap[$supplier])) {
            $priceField = $priceFieldMap[$supplier];
        }

        $sql = "SELECT $priceField FROM pricelist_$supplier WHERE sku='$sku' LIMIT 1";

        $price = 0;

        $result = $this->db->fetchOne($sql);
        if ($result) {
            $price = $result[$priceField];
        }

        return $price;
    }

    public function getCost($sku)
    {
        return $this->getPrice($sku);
    }

    public function getTitle($sku)
    {
        $supplier = $this->getSupplier($sku);

        $titleFieldMap = [
            'dh'  => 'short_desc',
            'ing' => 'part_desc_1',
        ];

        $titleField = 'title';
        if (isset($titleFieldMap[$supplier])) {
            $titleField = $titleFieldMap[$supplier];
        }

        $sql = "SELECT $titleField FROM pricelist_$supplier WHERE sku='$sku' LIMIT 1";

        $title = '';

        $result = $this->db->fetchOne($sql);
        if ($result) {
            $title = $result[$titleField];
        }

        return $title;
    }
}
