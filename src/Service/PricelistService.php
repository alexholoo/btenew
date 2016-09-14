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

if ($supplier !== 'syn') return 1.11;

        $sql = "SELECT price FROM pricelist_$supplier WHERE sku='$sku' LIMIT 1";

        $price = 0;

        $result = $this->db->fetchOne($sql);
        if ($result) {
            $price = $result['price'];
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

if ($supplier !== 'syn') return $supplier;

        $sql = "SELECT title FROM pricelist_$supplier WHERE sku='$sku' LIMIT 1";

        $title = '';

        $result = $this->db->fetchOne($sql);
        if ($result) {
            $title = $result['title'];
        }

        return $title;
    }
}
