<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrderShippingAddress extends Model
{
    public $id;
    public $orderNumber;
    public $shipToAddressLine1;
    public $shipToAddressLine2;
    public $shipToCity;
    public $shipToState;
    public $shipToZipCode;
    public $shipToCountry;
    public $shipToFirstName;
    public $shipToLastName;
    public $shipToCompany;
    public $shipToPhoneNumber;

    public function getSource()
    {
        return 'newegg_order_shipping_address';
    }

    public function initialize()
    {
    }
}
