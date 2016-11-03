<?php

namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * App\Models\MasterOrder
 */
class MasterOrder extends Orders
{
    public $channel;
    public $date;
    public $orderId;
    public $express;
    public $buyer;
    public $address;
    public $city;
    public $province;
    public $postalcode;
    public $country;
    public $phone;
    public $email;
    public $shipping;

    public function getSource()
    {
        return 'master_order';
    }

    public function initialize()
    {
        $this->hasMany("orderId", "MasterOrderItem", "orderId", ['alias' => 'items']);
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'         => 'id',
            'channel'    => 'channel',
            'date'       => 'date',
            'order_id'   => 'orderId',
            'express'    => 'express',
            'buyer'      => 'buyer',
            'address'    => 'address',
            'city'       => 'city',
            'province'   => 'province',
            'postalcode' => 'postalcode',
            'country'    => 'country',
            'phone'      => 'phone',
            'email'      => 'email',
            'shipping'   => 'shipping',
        );
    }
}
