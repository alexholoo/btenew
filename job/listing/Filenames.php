<?php

class Filenames
{
    protected static $filenames = [
        'amazon.ca.listing'       => 'E:/BTE/listing/amazon_ca_listing.txt',
        'amazon.us.listing'       => 'E:/BTE/listing/amazon_us_listing.txt',

        'bestbuy.listing'         => 'E:/BTE/listing/bestbuy_listing.csv',

        'ebay.bte.listing'        => 'E:/BTE/listing/ebay_listing_bte.csv',
        'ebay.odo.listing'        => 'E:/BTE/listing/ebay_listing_odo.csv',
        'ebay.gfs.listing'        => 'E:/BTE/listing/ebay_listing_gfs.csv',

        'newegg.ca.listing'       => 'E:/BTE/listing/newegg_ca_listing.csv',
        'newegg.us.listing'       => 'E:/BTE/listing/newegg_us_listing.csv',

        'rakuten.ca.listing'      => 'E:/BTE/listing/rakuten_ca_listing.txt',
        'rakuten.us.listing'      => 'E:/BTE/listing/rakuten_us_listing.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }
}
