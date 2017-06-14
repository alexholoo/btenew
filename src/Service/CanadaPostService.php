<?php

namespace Service;

use Phalcon\Di\Injectable;

class CanadaPostService extends Injectable
{
    public function getRate($order)
    {
        $address    = $order['address']['address'];
        $city       = $order['address']['city'];
        $province   = $order['address']['province'];
        $country    = $order['address']['country'];
        $postalcode = $order['address']['postalcode'];

        $sku    = $order['items'][0]['sku'];
        $info   = $this->skuService->getMasterSku($sku);

        $length = $info['Length'];
        $width  = $info['Width'];
        $depth  = $info['Depth'];
        $weight = $info['Weight'];

        //

        $rate = [];

        $rate['order_id']    = $order['order_id'];
        $rate['carrier']     = 'CanadaPost';
        $rate['sku']         = $sku;
        $rate['ship_to']     = "$city $province $country, $postalcode";
        $rate['dimension']   = "$length x $width x $depth in";
        $rate['weight']      = "$weight lbs";
        $rate['ship_method'] = '';
        $rate['rate']        = '0.00';

        return $rate;
    }
}
