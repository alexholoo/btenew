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
    const DH       = 'DH-';
    const ASI      = 'AS-';
    const INGRAM   = 'ING-';
    const SYNNEX   = 'SYN-';
    const TECHDATA = 'TD-';

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
}
