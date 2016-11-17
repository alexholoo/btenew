<?php

namespace Supplier\Model;

use Toolkit\Arr;

class OrderItem
{
    public $orderId;
    public $sku;
    public $price;
    public $qty;

    public function __construct($order)
    {
        $this->orderId = Arr::val($order, 'orderId');
        $this->sku     = Arr::val($order, 'sku');
        $this->price   = Arr::val($order, 'price');
        $this->qty     = Arr::val($order, 'qty');
    }
}
