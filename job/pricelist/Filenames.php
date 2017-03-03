<?php

class Filenames
{
    protected static $filenames = [
        // tracking
        'ebay.odo.shipping'        => 'E:/BTE/shipping/ebay_shipping_odo.csv',
        'newegg.ca.shipping'       => 'E:/BTE/shipping/newegg_ca_shipping.csv',
        'newegg.us.shipping'       => 'E:/BTE/shipping/newegg_us_shipping.csv',

        'rakuten.ca.shipping'      => 'E:/BTE/shipping/rakuten_tracking.txt',
        'rakuten.us.shipping'      => 'E:/BTE/shipping/rakuten_tracking.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}

//echo Filenames::get('rakuten.us.shipping'), PHP_EOL;
