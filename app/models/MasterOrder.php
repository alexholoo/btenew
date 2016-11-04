<?php

namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * App\Models\MasterOrder
 */
class MasterOrder extends Model
{
    public $channel;
    public $date;
    public $orderId;
    public $express;
    public $shipping;

    public function getSource()
    {
        return 'master_order';
    }

    public function initialize()
    {
        $this->hasMany("orderId", "MasterOrderItem",            "orderId", ['alias' => 'items']);
        $this->hasOne("orderId",  "MasterOrderShippingAddress", "orderId", ['alias' => 'shippingAddress']);
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
            'shipping'   => 'shipping',
        );
    }
}
