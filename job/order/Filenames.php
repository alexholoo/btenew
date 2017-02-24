<?php

class Filenames
{
    protected static $filenames = [

        'amazon.ca.order'          => 'E:/BTE/orders/amazon_ca_orders.csv',
        'amazon.us.order'          => 'E:/BTE/orders/amazon_us_orders.csv',

        'bestbuy.order'            => 'E:/BTE/orders/bestbuy_orders.csv',

        'ebay.bte.order'           => 'E:/BTE/orders/ebay_orders_bte.csv',
        'ebay.odo.order'           => 'E:/BTE/orders/ebay_orders_odo.csv',

        'newegg.ca.order'          => 'E:/BTE/orders/newegg_ca_orders.csv',
        'newegg.us.order'          => 'E:/BTE/orders/newegg_us_orders.csv',
        'newegg.master.order'      => 'E:/BTE/orders/newegg_master_orders.csv',

        'rakuten.ca.order'         => 'E:/BTE/orders/rakuten_ca_orders.txt',
        'rakuten.us.order'         => 'E:/BTE/orders/rakuten_us_orders.txt',
        'rakuten.master.order'     => 'E:/BTE/orders/rakuten_master_orders.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
