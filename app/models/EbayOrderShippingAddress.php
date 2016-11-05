<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrderShippingAddress extends Model
{
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

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'         => 'id',
            'OrderID'    => 'orderID',
            'Name'       => 'name',
            'Address'    => 'address',
            'Address2'   => 'address2',
            'City'       => 'city',
            'Province'   => 'province',
            'PostalCode' => 'postalCode',
            'Country'    => 'country',
            'Phone'      => 'phone',
        );
    }
}
