<?php

namespace Supplier;

class Factory
{
    /**
     * @param string $sku
     */
    public static function createClient($sku)
    {
        $client = NULL;

        $parts = explode('-', $sku);
        $supplier = strtoupper($parts[0]);

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
            throw new \Exception('Unknown supplier: ' . $supplier);
            break;
        }

        return $client;
    }
}
