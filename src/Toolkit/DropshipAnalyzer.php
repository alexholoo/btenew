<?php

namespace Toolkit;

class DropshipAnalyzer
{
    public static function get($sku, $price, $place, $express)
    {
        $parts = explode('-', $sku);
        $supplier = strtoupper($parts[0]);

        switch ($supplier) {
        case 'SYN':
            return Dropship_SYN::analyze($sku, $price, $place, $express);
            break;
        case 'TD':
            return Dropship_TD::analyze($sku, $price, $place, $express);
            break;
        case 'ING':
            return Dropship_ING::analyze($sku, $price, $place, $express);
            break;
        case 'DH':
            return Dropship_DH::analyze($sku, $price, $place, $express);
            break;
        }

        return false;  // no drop ship
    }
}

class Dropship_SYN
{
    protected static $dropshipProvinces = [ "ON", "AB", "BC", "NB", "NL", "NS", "YT", "NT", "PE" ];

    public static function analyze($sku, $price, $place, $express)
    {
        return in_array($place, self::$dropshipProvinces);
    }
}

class Dropship_TD
{
    protected static $dropshipProvinces  = [ "ON", "AB", "BC", "NB", "NL", "NS", "YT", "NT", "PE" ];

    public static function analyze($sku, $price, $place, $express)
    {
        if (in_array($place, self::$dropshipProvinces)) {
            if ($express == '1') { // TODO: ??
                // use drop ship for express order
                return true;
            }
            else if ($price > 300) {
                // use drop ship for expensive item, can adjust the amount accordingly
                return true;
            }
        }
        return false;
    }
}

class Dropship_ING
{
    // does ing drop ship to bc?
    protected static $dropshipProvinces = [ "ON", "AB", "NB", "NL", "NS", "YT", "NT", "PE" ];

    public static function analyze($sku, $price, $place, $express)
    {
        // use drop ship for all dropshipable orders
        return in_array($place, self::$dropshipProvinces);
    }
}

class Dropship_DH
{
    // location better with drop ship for DH items
    protected static $dropshipProvinces = [ "AB", "BC", "NB", "NL", "NS", "YT", "NT", "PE" ];

    public static function analyze($sku, $price, $place, $express)
    {
        if ($express == 'Yes') { // TODO: ??
            // use drop ship for express order
            return true;
        }
        else if ($price > 300) {
            // use drop ship for expensive item, can adjust the amount accordingly
            return true;
        }
        return in_array($place, self::$dropshipProvinces);
    }
}

//echo DropshipAnalyzer::get($sku, $price, $place, $express);
//var_dump(DropshipAnalyzer::get('DH-123',  200, 'ON', 1));
//var_dump(DropshipAnalyzer::get('SYN-123', 200, 'ON', 1));
//var_dump(DropshipAnalyzer::get('ING-123', 200, 'ON', 1));
//var_dump(DropshipAnalyzer::get('TD-123',  200, 'ON', 1));
//var_dump(DropshipAnalyzer::get('AS-123',  200, 'ON', 1));
