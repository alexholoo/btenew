<?php

class Filenames
{
    protected static $filenames = [
        'amazon.ca.tracking'    => 'E:/BTE/tracking/amazon_ca_dropship_tracking.csv',
        'amazon.us.tracking'    => 'E:/BTE/tracking/amazon_us_dropship_tracking.csv',

        'dh.tracking'           => 'E:/BTE/tracking/dh/DH-TRACKING',
        'techdata.tracking'     => 'E:/BTE/tracking/techdata/DH-TRACKING',
        'ingram.tracking'       => 'E:/BTE/tracking/ingram/DH-TRACKING',
        'synnex.tracking'       => 'E:/BTE/tracking/synnex/*.xml',

        'amazon.ca.shipping'    => 'E:/BTE/shipping/amazon_ca_shipment.txt',
        'amazon.us.shipping'    => 'E:/BTE/shipping/amazon_us_shipment.txt',

        'bestbuy.shipping'      => 'E:/BTE/shipping/bestbuy_shipping.csv',

        'ebay.bte.shipping'     => 'E:/BTE/shipping/ebay_shipping_bte.csv',
        'ebay.odo.shipping'     => 'E:/BTE/shipping/ebay_shipping_odo.csv',

        'newegg.ca.shipping'    => 'E:/BTE/shipping/newegg_ca_shipping.csv',
        'newegg.us.shipping'    => 'E:/BTE/shipping/newegg_us_shipping.csv',

        'rakuten.ca.shipping'   => 'E:/BTE/shipping/rakuten_tracking.txt',
        'rakuten.us.shipping'   => 'E:/BTE/shipping/rakuten_tracking.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}

//echo Filenames::get('rakuten.us.shipping'), PHP_EOL;
