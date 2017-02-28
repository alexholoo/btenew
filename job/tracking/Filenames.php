<?php

class Filenames
{
    protected static $filenames = [
        // tracking
        'canada.post.tracking'     => 'E:/BTE/tracking/cpc.xml',
        'eshipper.tracking'        => 'E:/BTE/tracking/eshipper_shipment.csv',
        'shippingeasy.tracking'    => 'E:/BTE/tracking/shippingeasy-shipping-report.csv',
        'solu.tracking'            => 'E:/BTE/tracking/solu_shipment.csv',
        'tnt.tracking'             => 'E:/BTE/tracking/tnt_shipments.csv',
        'ups.tracking'             => 'E:/BTE/tracking/ups_tracking.csv',
        'fedex.tracking'           => 'E:/BTE/tracking/fedex_tracking.txt',

        'amazon.ca.dropship'       => 'E:/BTE/tracking/amazon_ca_dropship_orders.csv',
        'amazon.us.dropship'       => 'E:/BTE/tracking/amazon_us_dropship_orders.csv',

        'amazon.ca.tracking'       => 'E:/BTE/tracking/amazon_ca_dropship_tracking.csv',
        'amazon.us.tracking'       => 'E:/BTE/tracking/amazon_us_dropship_tracking.csv',

        'amazon.ca.fba.tracking'   => 'E:/BTE/tracking/amazon_ca_FBA.csv',
        'amazon.us.fba.tracking'   => 'E:/BTE/tracking/amazon_us_FBA.csv',

        'dh.tracking'              => 'E:/BTE/tracking/DH-TRACKING',
        'techdata.tracking'        => 'E:/BTE/tracking/TD-tracking',
        'ingram.tracking'          => 'E:/BTE/tracking/ING-tracking',
        'synnex.tracking'          => 'E:/BTE/tracking/synnex',

        // shipping
        'amazon.ca.shipping'       => 'E:/BTE/shipping/amazon_ca_shipment.txt',
        'amazon.us.shipping'       => 'E:/BTE/shipping/amazon_us_shipment.txt',

        'bestbuy.shipping'         => 'E:/BTE/shipping/bestbuy_shipping.csv',

        'ebay.bte.shipping'        => 'E:/BTE/shipping/ebay_shipping_bte.csv',
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
