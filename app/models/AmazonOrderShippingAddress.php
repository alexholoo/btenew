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

    public function getSource()
    {
        return 'amazon_order_shipping_address';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'            => 'id',
            'OrderId'       => 'orderId',
            'Name'          => 'name',
            'AddressLine1'  => 'addressLine1',
            'AddressLine2'  => 'addressLine2',
            'AddressLine3'  => 'addressLine3',
            'City'          => 'city',
            'County'        => 'county',
            'District'      => 'district',
            'StateOrRegion' => 'stateOrRegion',
            'PostalCode'    => 'postalCode',
            'CountryCode'   => 'countryCode',
            'Phone'         => 'phone',
        );
    }
}
