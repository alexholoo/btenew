<?php
namespace App\Models;

use Phalcon\Mvc\Model;

/**
 * App\Models\Orders
 */
class Orders extends Model
{
    public $channel;
    public $date;
    public $orderId;
    public $mgnOrderId;
    public $express;
    public $buyer;
    public $address;
    public $city;
    public $province;
    public $postalcode;
    public $country;
    public $phone;
    public $email;
    public $sku;
    public $price;
    public $qty;
    public $shipping;
    public $productName;

    public function initialize()
    {
        $this->setSource('all_mgn_orders');
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'             => 'id',
            'channel'        => 'channel',
            'date'           => 'date',
            'order_id'       => 'orderId',
            'mgn_order_id'   => 'mgnOrderId',
            'express'        => 'express',
            'buyer'          => 'buyer',
            'address'        => 'address',
            'city'           => 'city',
            'province'       => 'province',
            'postalcode'     => 'postalcode',
            'country'        => 'country',
            'phone'          => 'phone',
            'email'          => 'email',
            'skus_sold'      => 'sku',
            'sku_price'      => 'price',
            'skus_qty'       => 'qty',
            'shipping'       => 'shipping',
            'product_name'   => 'productName'
        );
    }
}
