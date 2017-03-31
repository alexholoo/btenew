<?php

class Filenames
{
    const BASEDIR = 'E:/BTE';

    protected static $filenames = [

        /************************************************************
         * Listing Filenames
         ***********************************************************/
        'amazon.ca.listing'        => self::BASEDIR. '/listing/amazon_ca_listing.txt',
        'amazon.us.listing'        => self::BASEDIR. '/listing/amazon_us_listing.txt',

        'bestbuy.listing'          => self::BASEDIR. '/listing/bestbuy_listing.csv',

        'ebay.bte.listing'         => self::BASEDIR. '/listing/ebay_listing_bte.csv',
        'ebay.odo.listing'         => self::BASEDIR. '/listing/ebay_listing_odo.csv',
        'ebay.gfs.listing'         => self::BASEDIR. '/listing/ebay_listing_gfs.csv',

        'newegg.ca.listing'        => self::BASEDIR. '/listing/newegg_ca_listing.csv',
        'newegg.us.listing'        => self::BASEDIR. '/listing/newegg_us_listing.csv',

        'rakuten.ca.listing'       => self::BASEDIR. '/listing/rakuten_ca_listing.txt',
        'rakuten.us.listing'       => self::BASEDIR. '/listing/rakuten_us_listing.txt',

        /************************************************************
         * NewItems Filenames
         ***********************************************************/
        'amazon.ca.newitems'       => self::BASEDIR. '/newitems/amazon_ca_newitems.txt',
        'amazon.us.newitems'       => self::BASEDIR. '/newitems/amazon_us_newitems.txt',

        'bestbuy.newitems'         => self::BASEDIR. '/newitems/bestbuy_newitems.csv',

        'ebay.bte.newitems'        => self::BASEDIR. '/newitems/ebay_newitems_bte.csv',
        'ebay.odo.newitems'        => self::BASEDIR. '/newitems/ebay_newitems_oto.csv',
        'ebay.gfs.newitems'        => self::BASEDIR. '/newitems/ebay_newitems_gfs.csv',

        'newegg.ca.newitems'       => self::BASEDIR. '/newitems/newegg_ca_newitems.csv',
        'newegg.us.newitems'       => self::BASEDIR. '/newitems/newegg_us_newitems.csv',

        'rakuten.ca.newitems'      => self::BASEDIR. '/newitems/rakuten_ca_newitems.txt',
        'rakuten.us.newitems'      => self::BASEDIR. '/newitems/rakuten_us_newitems.txt',

        /************************************************************
         * Order Filenames
         ***********************************************************/
        'master.order'             => self::BASEDIR. '/orders/master_orders.csv',
        'dropship.order'           => self::BASEDIR. '/orders/dropship_orders.csv',

        'amazon.ca.order'          => self::BASEDIR. '/orders/amazon/amazon_ca_orders.txt',
        'amazon.us.order'          => self::BASEDIR. '/orders/amazon/amazon_us_orders.txt',

        'bestbuy.order'            => self::BASEDIR. '/orders/bestbuy/bestbuy_orders.csv',

        'ebay.bte.order'           => self::BASEDIR. '/orders/ebay/ebay_orders_bte.csv',
        'ebay.odo.order'           => self::BASEDIR. '/orders/ebay/ebay_orders_odo.csv',
        'ebay.gfs.order'           => self::BASEDIR. '/orders/ebay/ebay_orders_gfs.csv',

        'newegg.ca.order'          => self::BASEDIR. '/orders/newegg/orders_ca',  // folder
        'newegg.us.order'          => self::BASEDIR. '/orders/newegg/orders_us',  // folder

        // @see ../tracking/Filenames.php:41
        'newegg.ca.master.order'   => self::BASEDIR. '/orders/newegg/newegg_ca_master_orders.csv',
        'newegg.us.master.order'   => self::BASEDIR. '/orders/newegg/newegg_us_master_orders.csv',

        'rakuten.ca.order'         => self::BASEDIR. '/orders/rakuten/orders_ca', // folder
        'rakuten.us.order'         => self::BASEDIR. '/orders/rakuten/orders_us', // folder

        'rakuten.ca.master.order'  => self::BASEDIR. '/orders/rakuten/rakuten_ca_master_orders.csv',
        'rakuten.us.master.order'  => self::BASEDIR. '/orders/rakuten/rakuten_us_master_orders.csv',

        /************************************************************
         * Pricelist Filenames
         ***********************************************************/
        'dh.pricelist'             => self::BASEDIR. '/pricelist/DH-ITEMLIST',

        'tak.pricelist'            => self::BASEDIR. '/pricelist/atoz.csv',

        'synnex.pricelist.zip'     => self::BASEDIR. '/pricelist/SYN-c1150897.zip',
        'synnex.pricelist'         => self::BASEDIR. '/pricelist/SYN-c1150897.ap',

        'techdata.prodcod.zip'     => self::BASEDIR. '/pricelist/prodcode.zip',
        'techdata.prodlist.zip'    => self::BASEDIR. '/pricelist/prodlist.zip',
        'techdata.prodmast'        => self::BASEDIR. '/pricelist/prodmast.txt',
        'techdata.pricelist'       => self::BASEDIR. '/pricelist/prodmast.txt',

        'ingram.pricelist.zip'     => self::BASEDIR. '/pricelist/ING-price.zip',
        'ingram.pricelist'         => self::BASEDIR. '/pricelist/ING-price.csv',

        /************************************************************
         * PriceQty Filenames
         ***********************************************************/
        'amazon.ca.priceqty'       => self::BASEDIR. '/priceqty/amazon_ca_priceqty.txt',
        'amazon.us.priceqty'       => self::BASEDIR. '/priceqty/amazon_us_priceqty.txt',

        'bestbuy.priceqty'         => self::BASEDIR. '/priceqty/bestbuy_priceqty.csv',

        'ebay.bte.priceqty'        => self::BASEDIR. '/priceqty/ebay_priceqty_bte.csv',
        'ebay.odo.priceqty'        => self::BASEDIR. '/priceqty/ebay_priceqty_odo.csv',
        'ebay.gfs.priceqty'        => self::BASEDIR. '/priceqty/ebay_priceqty_gfs.csv',

        'newegg.ca.priceqty'       => self::BASEDIR. '/priceqty/newegg_ca_priceqty.csv',
        'newegg.us.priceqty'       => self::BASEDIR. '/priceqty/newegg_us_priceqty.csv',

        'rakuten.ca.priceqty'      => self::BASEDIR. '/priceqty/rakuten_ca_priceqty.txt',
        'rakuten.us.priceqty'      => self::BASEDIR. '/priceqty/rakuten_us_priceqty.txt',

        /************************************************************
         * Tracking/Shipment Filenames
         ***********************************************************/
        // tracking
        'canada.post.tracking'     => self::BASEDIR. '/tracking/CPC.xml',
        'eshipper.tracking'        => self::BASEDIR. '/tracking/EShipper_shipment.csv',
        'shippingeasy.tracking'    => self::BASEDIR. '/tracking/ShippingEasy-shipping-report.csv',
        'solu.tracking'            => self::BASEDIR. '/tracking/SOLU_shipment.csv',
        'tnt.tracking'             => self::BASEDIR. '/tracking/TNT_shipments.csv',
        'ups.tracking'             => self::BASEDIR. '/tracking/UPS_tracking.csv',
        'fedex.tracking'           => self::BASEDIR. '/tracking/FEDEX_tracking.txt',

        'amazon.ca.dropship'       => self::BASEDIR. '/tracking/amazon_ca_dropship_orders.csv',
        'amazon.us.dropship'       => self::BASEDIR. '/tracking/amazon_us_dropship_orders.csv',

        'amazon.ca.tracking'       => self::BASEDIR. '/tracking/amazon_ca_dropship_tracking.csv',
        'amazon.us.tracking'       => self::BASEDIR. '/tracking/amazon_us_dropship_tracking.csv',

        'amazon.ca.fba.tracking'   => self::BASEDIR. '/tracking/amazon_ca_FBA.csv',
        'amazon.us.fba.tracking'   => self::BASEDIR. '/tracking/amazon_us_FBA.csv',

        'dh.tracking'              => self::BASEDIR. '/tracking/DH-tracking',
        'techdata.tracking'        => self::BASEDIR. '/tracking/TD-tracking',
        'ingram.tracking'          => self::BASEDIR. '/tracking/ING-tracking',
        'synnex.tracking'          => self::BASEDIR. '/tracking/synnex', // folder

        // shipping
        'amazon.ca.shipping'       => self::BASEDIR. '/shipping/amazon_ca_shipment.txt',
        'amazon.us.shipping'       => self::BASEDIR. '/shipping/amazon_us_shipment.txt',

        'bestbuy.shipping'         => self::BASEDIR. '/shipping/bestbuy_shipping.csv',

        'ebay.bte.shipping'        => self::BASEDIR. '/shipping/ebay_shipping_bte.csv',
        'ebay.odo.shipping'        => self::BASEDIR. '/shipping/ebay_shipping_odo.csv',
        'ebay.gfs.shipping'        => self::BASEDIR. '/shipping/ebay_shipping_gfs.csv',

        'newegg.ca.shipping'       => self::BASEDIR. '/shipping/newegg_ca_shipping.csv',
        'newegg.us.shipping'       => self::BASEDIR. '/shipping/newegg_us_shipping.csv',

        // @see ../order/Filenames.php:21
        'newegg.ca.master.order'   => self::BASEDIR. '/orders/newegg/newegg_ca_master_orders.csv',
        'newegg.us.master.order'   => self::BASEDIR. '/orders/newegg/newegg_us_master_orders.csv',

        'rakuten.ca.shipping'      => self::BASEDIR. '/shipping/rakuten_tracking.txt',
        'rakuten.us.shipping'      => self::BASEDIR. '/shipping/rakuten_tracking.txt',
    ];

    public static function get($key)
    {
        return self::$filenames[$key] ?? '';
    }

    public static function makeDirs()
    {
        foreach (self::$filenames as $filename) {
            $dirname = dirname($filename);
            if (!file_exists($dirname)) {
                echo "create dir $dirname\n";
                mkdir($dirname, 0777, true);
            }
        }
    }
}

#Filenames::makeDirs();
