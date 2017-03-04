<?php

class Filenames
{
    protected static $filenames = [
        'amazon.ca.newitems'       => 'E:/BTE/newitems/amazon_ca_newitems.txt',
        'amazon.us.newitems'       => 'E:/BTE/newitems/amazon_us_newitems.txt',

        'bestbuy.newitems'         => 'E:/BTE/newitems/bestbuy_newitems.csv',

        'ebay.bte.newitems'        => 'E:/BTE/newitems/ebay_newitems_bte.csv',
        'ebay.odo.newitems'        => 'E:/BTE/newitems/ebay_newitems_oto.csv',

        'newegg.ca.newitems'       => 'E:/BTE/newitems/newegg_ca_newitems.csv',
        'newegg.us.newitems'       => 'E:/BTE/newitems/newegg_us_newitems.csv',

        'rakuten.ca.newitems'      => 'E:/BTE/newitems/rakuten_ca_newitems.txt',
        'rakuten.us.newitems'      => 'E:/BTE/newitems/rakuten_us_newitems.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
