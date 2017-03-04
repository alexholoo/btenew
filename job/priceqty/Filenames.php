<?php

class Filenames
{
    protected static $filenames = [
        'amazon.ca.priceqty'       => 'E:/BTE/priceqty/amazon_ca_priceqty.txt',
        'amazon.us.priceqty'       => 'E:/BTE/priceqty/amazon_us_priceqty.txt',

        'bestbuy.priceqty'         => 'E:/BTE/priceqty/bestbuy_priceqty.csv',

        'ebay.bte.priceqty'        => 'E:/BTE/priceqty/ebay_priceqty_bte.csv',
        'ebay.odo.priceqty'        => 'E:/BTE/priceqty/ebay_priceqty_odo.csv',

        'newegg.ca.priceqty'       => 'E:/BTE/priceqty/newegg_ca_priceqty.csv',
        'newegg.us.priceqty'       => 'E:/BTE/priceqty/newegg_us_priceqty.csv',

        'rakuten.ca.priceqty'      => 'E:/BTE/priceqty/rakuten_ca_priceqty.txt',
        'rakuten.us.priceqty'      => 'E:/BTE/priceqty/rakuten_us_priceqty.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
