<?php

namespace Supplier;

class Factory
{
    /**
     * @param string $sku
     * @param string $for
     */
    public static function createClient($sku, $for)
    {
        $parts = explode('-', $sku);
        $supplier = strtoupper($parts[0]);

        $for = strtoupper($for);

        if ($for == 'PA' || $for == 'PNA') {
            return self::createPriceAvailabilityClient($supplier);
        } else if ($for == 'PO') {
            return self::createPurchaseOrderClient($supplier);
        }

        throw new \Exception("Invalid Parameter: $for");
    }

    /**
     * @param string $supplier
     */
    protected static function createPriceAvailabilityClient($supplier)
    {
        $client = NULL;

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
            throw new \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }

    /**
     * @param string $supplier
     */
    protected static function createPurchaseOrderClient($supplier)
    {
        $client = NULL;

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
            throw new \Exception('Unknown supplier ID: ' . $supplier);
            break;
        }

        return $client;
    }
}
