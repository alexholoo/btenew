<?php

namespace Service;

use Phalcon\Di\Injectable;

class AmazonService extends Injectable
{
    public function isAmazonOrder($order)
    {
        return substr($order['channel'], 0, 6) == 'Amazon';
        #return (boolean)preg_match('/^\d{3}-\d{7}-\d{7}$/', $orderId);
    }

    public function isOrderCanceled($order)
    {
        $store = $this->getStore($order);

        $orderId = $order['orderId'];
        //$orderId = '701-8728845-2735459'; // Canceled

        $order = new \AmazonOrder($store);
        $order->setOrderId($orderId);
        $order->fetchOrder();

        return $order->getOrderStatus() == 'Canceled';
    }

    protected function getStore($order)
    {
        $store = 'bte-amazon-ca';

        if ($order['channel'] == 'Amazon-US') {
            $store = 'bte-amazon-us';
        }

        return $store;
    }

    public function getSellingPrice($sku, $market = 'CA')
    {
        $table = 'amazon_ca_listings';
        if ($market == 'US') {
            $table = 'amazon_us_listings';
        }

        $sql = "SELECT price FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);

        return $result ? $result['price'] : 0;
    }

    public function getAsinFromSku($sku, $market = 'CA')
    {
        $table = 'amazon_ca_listings';
        if ($market == 'US') {
            $table = 'amazon_us_listings';
        }

        $sql = "SELECT asin FROM $table WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result ? $result['asin'] : '';
    }

    public function getAsin($sku, $market = 'CA')
    {
        return $this->getAsinFromSku($sku, $market);
    }

    public function getImageUrl($sku, $size = 'L')
    {
        $row = $this->db->fetchOne("SELECT * FROM amazon_image WHERE sku='$sku'");

        if ($row) {
            if ($size == 'L') return $row['image_big'];
            if ($size == 'S') return $row['image_small'];
        }

        return false;
    }

    // the url that this method returned sometimes points to a blank image
    public function getImageUrlFromAsin($asin, $size = 'L')
    {
        if ($size == 'T') $size = 'THUMBZZZ'; // 40 x 60 pixels, very small
        if ($size == 'M') $size = 'MZZZZZZZ'; // 93 x 140 pixels, standard size
        if ($size == 'L') $size = 'LZZZZZZZ'; // 317 x 475 pixels, very large

        return "http://images.amazon.com/images/P/$asin.01.$size.jpg";
    }

    public function getDescription($sku)
    {
        $asin = $this->getAsin($sku, 'US');

        $sql = "SELECT * FROM amazon_asin_desc WHERE asin='$asin'";
        $result = $this->db->fetchOne($sql);

        return $result ? $result['description'].$result['feature'] : '';
    }

    public function doSomething()
    {
        fpr(__FILE__."\n".__METHOD__);

        $ebayService = $this->di->get('ebayService');
        $ebayService->doSomething();
    }
}
