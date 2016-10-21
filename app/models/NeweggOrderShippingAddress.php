<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrderShippingAddress extends Model
{
    public $id;
    public $OrderNumber;
    public $ShipToAddressLine1;
    public $ShipToAddressLine2;
    public $ShipToCity;
    public $ShipToState;
    public $ShipToZipCode;
    public $ShipToCountry;
    public $ShipToFirstName;
    public $ShipToLastName;
    public $ShipToCompany;
    public $ShipToPhoneNumber;

    public function getSource()
    {
        return 'newegg_order_shipping_address';
    }

    public function initialize()
    {
    }
}
