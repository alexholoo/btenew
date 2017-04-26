<?php

class Filename
{
    const BASEDIR = 'E:/BTE';

    /************************************************************
     * Listing Filenames
     ***********************************************************/
    const AMAZON_CA_LISTING         =  self::BASEDIR. '/listing/amazon_ca_listing.txt';
    const AMAZON_US_LISTING         =  self::BASEDIR. '/listing/amazon_us_listing.txt';

    const BESTBUY_LISTING           =  self::BASEDIR. '/listing/bestbuy_listing.csv';

    const EBAY_BTE_LISTING          =  self::BASEDIR. '/listing/ebay_listing_bte.csv';
    const EBAY_ODO_LISTING          =  self::BASEDIR. '/listing/ebay_listing_odo.csv';
    const EBAY_GFS_LISTING          =  self::BASEDIR. '/listing/ebay_listing_gfs.csv';

    const NEWEGG_CA_LISTING         =  self::BASEDIR. '/listing/newegg_ca_listing.csv';
    const NEWEGG_US_LISTING         =  self::BASEDIR. '/listing/newegg_us_listing.csv';

    const RAKUTEN_CA_LISTING        =  self::BASEDIR. '/listing/rakuten_ca_listing.txt';
    const RAKUTEN_US_LISTING        =  self::BASEDIR. '/listing/rakuten_us_listing.txt';

    /************************************************************
     * NewItems Filenames
     ***********************************************************/
    const AMAZON_CA_NEWITEMS        =  self::BASEDIR. '/newitems/amazon_ca_newitems.txt';
    const AMAZON_US_NEWITEMS        =  self::BASEDIR. '/newitems/amazon_us_newitems.txt';

    const BESTBUY_NEWITEMS          =  self::BASEDIR. '/newitems/bestbuy_newitems.csv';

    const EBAY_BTE_NEWITEMS         =  self::BASEDIR. '/newitems/ebay_newitems_bte.csv';
    const EBAY_ODO_NEWITEMS         =  self::BASEDIR. '/newitems/ebay_newitems_oto.csv';
    const EBAY_GFS_NEWITEMS         =  self::BASEDIR. '/newitems/ebay_newitems_gfs.csv';

    const NEWEGG_CA_NEWITEMS        =  self::BASEDIR. '/newitems/newegg_ca_newitems.csv';
    const NEWEGG_US_NEWITEMS        =  self::BASEDIR. '/newitems/newegg_us_newitems.csv';

    const RAKUTEN_CA_NEWITEMS       =  self::BASEDIR. '/newitems/rakuten_ca_newitems.txt';
    const RAKUTEN_US_NEWITEMS       =  self::BASEDIR. '/newitems/rakuten_us_newitems.txt';

    /************************************************************
     * Order Filenames
     ***********************************************************/
    const MASTER_ORDER              =  self::BASEDIR. '/orders/master_orders.csv';
    const DROPSHIP_ORDER            =  self::BASEDIR. '/orders/dropship_orders.csv';

    const AMAZON_CA_ORDER           =  self::BASEDIR. '/orders/amazon/amazon_ca_orders.txt';
    const AMAZON_US_ORDER           =  self::BASEDIR. '/orders/amazon/amazon_us_orders.txt';

    const BESTBUY_ORDER             =  self::BASEDIR. '/orders/bestbuy/bestbuy_orders.csv';

    const EBAY_BTE_ORDER            =  self::BASEDIR. '/orders/ebay/ebay_orders_bte.csv';
    const EBAY_ODO_ORDER            =  self::BASEDIR. '/orders/ebay/ebay_orders_odo.csv';
    const EBAY_GFS_ORDER            =  self::BASEDIR. '/orders/ebay/ebay_orders_gfs.csv';

    const NEWEGG_CA_ORDER           =  self::BASEDIR. '/orders/newegg/orders_ca';  // folder
    const NEWEGG_US_ORDER           =  self::BASEDIR. '/orders/newegg/orders_us';  // folder

    const NEWEGG_CA_MASTER_ORDER    =  self::BASEDIR. '/orders/newegg/newegg_ca_master_orders.csv';
    const NEWEGG_US_MASTER_ORDER    =  self::BASEDIR. '/orders/newegg/newegg_us_master_orders.csv';

    const RAKUTEN_CA_ORDER          =  self::BASEDIR. '/orders/rakuten/orders_ca'; // folder
    const RAKUTEN_US_ORDER          =  self::BASEDIR. '/orders/rakuten/orders_us'; // folder

    const RAKUTEN_CA_MASTER_ORDER   =  self::BASEDIR. '/orders/rakuten/rakuten_ca_master_orders.csv';
    const RAKUTEN_US_MASTER_ORDER   =  self::BASEDIR. '/orders/rakuten/rakuten_us_master_orders.csv';

    /************************************************************
     * Pricelist Filenames
     ***********************************************************/
    const DH_PRICELIST              =  self::BASEDIR. '/pricelist/DH-ITEMLIST';

    const TAK_PRICELIST             =  self::BASEDIR. '/pricelist/atoz.csv';

    const SYNNEX_PRICELIST_ZIP      =  self::BASEDIR. '/pricelist/SYN-c1150897.zip';
    const SYNNEX_PRICELIST          =  self::BASEDIR. '/pricelist/SYN-c1150897.ap';

    const TECHDATA_PRODCOD_ZIP      =  self::BASEDIR. '/pricelist/prodcode.zip';
    const TECHDATA_PRODLIST_ZIP     =  self::BASEDIR. '/pricelist/prodlist.zip';
    const TECHDATA_PRODMAST         =  self::BASEDIR. '/pricelist/prodmast.txt';
    const TECHDATA_PRICELIST        =  self::BASEDIR. '/pricelist/prodmast.txt';

    const INGRAM_PRICELIST_ZIP      =  self::BASEDIR. '/pricelist/ING-price.zip';
    const INGRAM_PRICELIST          =  self::BASEDIR. '/pricelist/ING-price.csv';

    /************************************************************
     * PriceQty Filenames
     ***********************************************************/
    const AMAZON_CA_PRICEQTY        =  self::BASEDIR. '/priceqty/amazon_ca_priceqty.txt';
    const AMAZON_US_PRICEQTY        =  self::BASEDIR. '/priceqty/amazon_us_priceqty.txt';

    const BESTBUY_PRICEQTY          =  self::BASEDIR. '/priceqty/bestbuy_priceqty.csv';

    const EBAY_BTE_PRICEQTY         =  self::BASEDIR. '/priceqty/ebay_priceqty_bte.csv';
    const EBAY_ODO_PRICEQTY         =  self::BASEDIR. '/priceqty/ebay_priceqty_odo.csv';
    const EBAY_GFS_PRICEQTY         =  self::BASEDIR. '/priceqty/ebay_priceqty_gfs.csv';

    const NEWEGG_CA_PRICEQTY        =  self::BASEDIR. '/priceqty/newegg_ca_priceqty.csv';
    const NEWEGG_US_PRICEQTY        =  self::BASEDIR. '/priceqty/newegg_us_priceqty.csv';

    const RAKUTEN_CA_PRICEQTY       =  self::BASEDIR. '/priceqty/rakuten_ca_priceqty.txt';
    const RAKUTEN_US_PRICEQTY       =  self::BASEDIR. '/priceqty/rakuten_us_priceqty.txt';

    /************************************************************
     * Tracking/Shipment Filenames
     ***********************************************************/
    // tracking
    const CANADA_POST_TRACKING      =  self::BASEDIR. '/tracking/CPC.xml';
    const ESHIPPER_TRACKING         =  self::BASEDIR. '/tracking/EShipper_shipment.csv';
    const SHIPPINGEASY_TRACKING     =  self::BASEDIR. '/tracking/ShippingEasy-shipping-report.csv';
    const SOLU_TRACKING             =  self::BASEDIR. '/tracking/SOLU_shipment.csv';
    const TNT_TRACKING              =  self::BASEDIR. '/tracking/TNT_shipments.csv';
    const UPS_TRACKING              =  self::BASEDIR. '/tracking/UPS_tracking.csv';
    const FEDEX_TRACKING            =  self::BASEDIR. '/tracking/FEDEX_tracking.txt';

    const AMAZON_CA_DROPSHIP        =  self::BASEDIR. '/tracking/amazon_ca_dropship_orders.csv';
    const AMAZON_US_DROPSHIP        =  self::BASEDIR. '/tracking/amazon_us_dropship_orders.csv';

    const AMAZON_CA_TRACKING        =  self::BASEDIR. '/tracking/amazon_ca_dropship_tracking.csv';
    const AMAZON_US_TRACKING        =  self::BASEDIR. '/tracking/amazon_us_dropship_tracking.csv';

    const AMAZON_CA_FBA_TRACKING    =  self::BASEDIR. '/tracking/amazon_ca_FBA.csv';
    const AMAZON_US_FBA_TRACKING    =  self::BASEDIR. '/tracking/amazon_us_FBA.csv';

    const DH_TRACKING               =  self::BASEDIR. '/tracking/DH-tracking.csv';
    const TECHDATA_TRACKING         =  self::BASEDIR. '/tracking/TD-tracking.csv';
    const INGRAM_TRACKING           =  self::BASEDIR. '/tracking/ING-tracking.csv';
    const ASI_TRACKING              =  self::BASEDIR. '/tracking/ASI-tracking.csv';
    const SYNNEX_TRACKING           =  self::BASEDIR. '/tracking/synnex'; // folder

    // shipping
    const AMAZON_CA_SHIPPING        =  self::BASEDIR. '/shipping/amazon_ca_shipment.txt';
    const AMAZON_US_SHIPPING        =  self::BASEDIR. '/shipping/amazon_us_shipment.txt';

    const BESTBUY_SHIPPING          =  self::BASEDIR. '/shipping/bestbuy_shipping.csv';

    const EBAY_BTE_SHIPPING         =  self::BASEDIR. '/shipping/ebay_shipping_bte.csv';
    const EBAY_ODO_SHIPPING         =  self::BASEDIR. '/shipping/ebay_shipping_odo.csv';
    const EBAY_GFS_SHIPPING         =  self::BASEDIR. '/shipping/ebay_shipping_gfs.csv';

    const NEWEGG_CA_SHIPPING        =  self::BASEDIR. '/shipping/newegg_ca_shipping.csv';
    const NEWEGG_US_SHIPPING        =  self::BASEDIR. '/shipping/newegg_us_shipping.csv';

    const RAKUTEN_CA_SHIPPING       =  self::BASEDIR. '/shipping/rakuten_tracking.txt';
    const RAKUTEN_US_SHIPPING       =  self::BASEDIR. '/shipping/rakuten_tracking.txt';
}

class Filenames
{
    public static function get($key)
    {
        $key = str_replace('.', '_', strtoupper($key));
        return constant("Filename::$key");
    }
}

#echo Filename::AMAZON_CA_LISTING, PHP_EOL;
#echo Filenames::get('amazon.ca.listing'), PHP_EOL;
