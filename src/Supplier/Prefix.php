<?php

namespace Supplier;

/**
 * $prefix = \Supplier\Prefix::DH;
 * $prefix = \Supplier\Prefix::ASI;
 * $prefix = \Supplier\Prefix::INGRAM;
 * $prefix = \Supplier\Prefix::SYNNEX;
 * $prefix = \Supplier\Prefix::TECHDATA;
 *
 * $all = \Supplier\Prefix::all();
 */
class Prefix
{
    const DH       = 'DH';  // no '-' suffix
    const ASI      = 'AS';  // no '-' suffix
    const INGRAM   = 'ING'; // no '-' suffix
    const SYNNEX   = 'SYN'; // no '-' suffix
    const TECHDATA = 'TD';  // no '-' suffix

    public static function all()
    {
        return [
            self::DH,
            self::ASI,
            self::INGRAM,
            self::SYNNEX,
            self::TECHDATA
        ];
    }

    public static function fromSku($sku)
    {
        $parts = explode('-', $sku);
        $supplier = strtoupper($parts[0]);

        // TODO: unknown supplier
        // TODO: freeshipping prefix

        return $supplier;
    }
}
