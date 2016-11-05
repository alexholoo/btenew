<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrder extends Model
{
    public $orderNumber;
    public $orderDateTime;
    public $salesChannel;
    public $fulfillmentOption;
    public $orderCustomerEmail;
    public $orderShippingMethod;
    public $orderShippingTotal;
    public $GSTorHSTTotal;
    public $PSTorQSTTotal;
    public $orderTotal;

    public function getSource()
    {
        return 'newegg_order_report';
    }

    public function initialize()
    {
        $this->hasMany('orderNumber', 'App\\Models\\NeweggOrderItem',            'orderNumber', ['alias' => 'items']);
        $this->hasOne('orderNumber',  'App\\Models\\NeweggOrderShippingAddress', 'orderNumber', ['alias' => 'shippingAddress']);
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                  => 'id',
            'OrderNumber'         => 'orderNumber',
            'OrderDateTime'       => 'orderDateTime',
            'SalesChannel'        => 'salesChannel',
            'FulfillmentOption'   => 'fulfillmentOption',
            'OrderCustomerEmail'  => 'orderCustomerEmail',
            'OrderShippingMethod' => 'orderShippingMethod',
            'OrderShippingTotal'  => 'orderShippingTotal',
            'GSTorHSTTotal'       => 'GSTorHSTTotal',
            'PSTorQSTTotal'       => 'PSTorQSTTotal',
            'OrderTotal'          => 'orderTotal',
        );
    }
}
