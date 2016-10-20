<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrderShippingAddress extends Model
{
    public $id;
    public $orderID;
    public $name;
    public $address;
    public $address2;
    public $city;
    public $province;
    public $postalCode;
    public $country;
    public $phone;

    public function getSource()
    {
        return 'ebay_order_shipping_address';
    }

    public function initialize()
    {
    }
}
