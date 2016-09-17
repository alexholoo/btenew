<?php

namespace Supplier;

class Supplier
{
    protected static $cache = [];

    /**
     * @param  string $sku
     * @return Supplier\Client|null
     */
    public static function createClient($sku)
    {
        $client = NULL;

        $supplier = \Supplier\Prefix::fromSku($sku);

        if (isset(self::$cache[$supplier])) {
            return self::$cache[$supplier];
        }

        switch($supplier) {
        case \Supplier\Prefix::DH:
            $client = new \Supplier\DH\Client();
            break;

        case \Supplier\Prefix::SYNNEX:
            $client = new \Supplier\Synnex\Client();
            break;

        case \Supplier\Prefix::INGRAM:
            $client = new \Supplier\Ingram\Client();
            break;

        case \Supplier\Prefix::TECHDATA:
            $client = new \Supplier\Techdata\Client();
            break;

        case \Supplier\Prefix::ASI:
            $client = new \Supplier\ASI\Client();
            break;

        default:
            //throw new \Exception('Unknown supplier: ' . $supplier);
            break;
        }

        self::$cache[$supplier] = $client;

        return $client;
    }
}
