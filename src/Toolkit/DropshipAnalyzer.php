<?php

namespace Toolkit;

class DropshipAnalyzer
{
    public static function get($sku, $price, $place, $express)
    {
        $syn_dropship = [ "ON","AB","BC","NB","NL","NS","YT","NT","PE" ];
        $dh_dropship  = [ "AB","BC","NB","NL","NS","YT","NT","PE" ]; // location better with drop ship for DH items
        $td_dropship  = [ "ON","AB","BC","NB","NL","NS","YT","NT","PE" ];
        $ing_dropship = [ "ON","AB","NB","NL","NS","YT","NT","PE" ]; // does ing drop ship to bc?

        $parts = explode('-', $sku);
        $supplier = $parts[0];

        if ($supplier == 'AS') {
            // no drop ship for asi item
            return false;
        }

        if ($supplier == 'SYN') {
            return in_array($place, $syn_dropship);
        }

        if ($supplier == 'TD') {
            if (in_array($place, $td_dropship)) {
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

        if ($supplier == 'ING') {
            // use drop ship for all dropshipable orders
            return in_array($place, $ing_dropship);
        }

        if ($supplier == 'DH') {
            if ($express == 'Yes') { // TODO: ??
                // use drop ship for express order
                return true;
            }
            else if ($price > 300) {
                // use drop ship for expensive item, can adjust the amount accordingly
                return true;
            }
            return in_array ($place ,$dh_dropship);
        }

        return false;
    }
}

//echo DropshipAnalyzer::get($sku, $price, $place, $express);
