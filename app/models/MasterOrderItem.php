<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class MasterOrderItem extends Model
{
    public $id;
    public $orderId;
    public $sku;
    public $price;
    public $qty;

    public function getSource()
    {
        return 'master_order_item';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'       => 'id',
            'order_id' => 'orderId',
            'sku'      => 'sku',
            'price'    => 'price',
            'qty'      => 'qty',
        );
    }
}
