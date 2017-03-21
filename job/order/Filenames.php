<?php

class Filenames
{
    protected static $filenames = [

        'master.order'             => 'E:/BTE/orders/master_orders.csv',
        'dropship.order'           => 'E:/BTE/orders/dropship_orders.csv',

        'amazon.ca.order'          => 'E:/BTE/orders/amazon/amazon_ca_orders.csv',
        'amazon.us.order'          => 'E:/BTE/orders/amazon/amazon_us_orders.csv',

        'bestbuy.order'            => 'E:/BTE/orders/bestbuy/bestbuy_orders.csv',

        'ebay.bte.order'           => 'E:/BTE/orders/ebay/ebay_orders_bte.csv',
        'ebay.odo.order'           => 'E:/BTE/orders/ebay/ebay_orders_odo.csv',

        'newegg.ca.order'          => 'E:/BTE/orders/newegg/orders_ca',  // folder
        'newegg.us.order'          => 'E:/BTE/orders/newegg/orders_us',  // folder

        // @see ../tracking/Filenames.php:41
        'newegg.ca.master.order'   => 'E:/BTE/orders/newegg/newegg_ca_master_orders.csv',
        'newegg.us.master.order'   => 'E:/BTE/orders/newegg/newegg_us_master_orders.csv',

        'rakuten.ca.order'         => 'E:/BTE/orders/rakuten/orders_ca', // folder
        'rakuten.us.order'         => 'E:/BTE/orders/rakuten/orders_us', // folder

        'rakuten.ca.master.order'  => 'E:/BTE/orders/rakuten/rakuten_ca_master_orders.csv',
        'rakuten.us.master.order'  => 'E:/BTE/orders/rakuten/rakuten_us_master_orders.csv',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
