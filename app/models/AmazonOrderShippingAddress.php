<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrderShippingAddress extends Model
{
    public $OrderId;
    public $Name;
    public $AddressLine1;
    public $AddressLine2;
    public $AddressLine3;
    public $City;
    public $County;
    public $District;
    public $StateOrRegion;
    public $PostalCode;
    public $CountryCode;
    public $Phone;

    public function initialize()
    {
        $this->setSource("amazon_order_shipping_address");
    }
}
