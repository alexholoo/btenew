<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrderShippingAddress extends Model
{
    public $orderId;
    public $name;
    public $addressLine1;
    public $addressLine2;
    public $addressLine3;
    public $city;
    public $county;
    public $district;
    public $stateOrRegion;
    public $postalCode;
    public $countryCode;
    public $phone;

    public function initialize()
    {
        $this->setSource("amazon_order_shipping_address");
    }
}
