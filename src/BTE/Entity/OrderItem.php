<?php

namespace BTE\Entity;

use Toolkit\Arr;

class OrderItem
{
    public $orderId;
    public $sku;
    public $price;
    public $qty;

    public function __construct($info)
    {
        $this->orderId = Arr::val($info, 'orderId');
        $this->sku     = Arr::val($info, 'sku');
        $this->price   = Arr::val($info, 'price');
        $this->qty     = Arr::val($info, 'qty');
    }
}
