<?php

namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * App\Models\MasterOrderShippingAddress
 */
class MasterOrderShippingAddress extends Model
{
    public $date;
    public $orderId;
    public $buyer;
    public $address;
    public $city;
    public $province;
    public $postalcode;
    public $country;
    public $phone;
    public $email;

    public function getSource()
    {
        return 'master_order_shipping_address';
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
            'date'       => 'date',
            'order_id'   => 'orderId',
            'buyer'      => 'buyer',
            'address'    => 'address',
            'city'       => 'city',
            'province'   => 'province',
            'postalcode' => 'postalcode',
            'country'    => 'country',
            'phone'      => 'phone',
            'email'      => 'email',
        );
    }
}
