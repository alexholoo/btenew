<?php

namespace Service;

use Phalcon\Di\Injectable;

class NeweggService extends Injectable
{
    public function findSku($sku, $site)
    {
        $table = ($site == 'US') ? 'newegg_us_listing' : 'newegg_ca_listing';

        $sql = "SELECT * FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }

    public function getShipMethodName($code)
    {
        $shipMethods = $this->getShipMethods();
        return isset($shipMethods[$code]) ? $shipMethods[$code] : 'NA' ;
    }

    public function getShipMethodCode($shipMethodName)
    {
        // $shipMethodName might be one of the following
        # Expedited Shipping (3-5 business days)
        # International Expedited Shipping (3-5 business days)
        # One-Day Shipping(Next day)
        # Standard Shipping (5-7 business days)
        # Two-Day Shipping(2 business days)

        $shipMethods = $this->getShipMethods();

        foreach ($shipMethods as $code => $name) {
            if (strpos($shipMethodName, $name) !== false) {
                return $code;
            }
        }

        return 0; // Standard
    }

    protected function getShipMethods()
    {
        static $methods = [
            0 => 'Standard',
            1 => 'Economy',
            2 => 'Expedited',
            3 => 'One-Day',
            4 => 'Two-Day'
        ];

        return $methods;
    }
}
