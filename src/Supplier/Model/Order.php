<?php

namespace Supplier\Model;

use Toolkit\Arr;

class Order
{
    public $channel;
    public $date;
    public $orderId;
    public $express;
    public $shippingAddress;
    public $items = [];
    public $shipMethod;
    public $branch;
    public $comment;
    public $notifyEmail;

    public function __construct($info)
    {
        $this->channel     = Arr::val($info, 'channel');
        $this->date        = Arr::val($info, 'date');
        $this->orderId     = Arr::val($info, 'orderId');
        $this->express     = Arr::val($info, 'express');
        $this->shipMethod  = Arr::val($info, 'shipMethod');
        $this->branch      = Arr::val($info, 'branch');
        $this->comment     = Arr::val($info, 'comment');
        $this->notifyEmail = Arr::val($info, 'notifyEmail');

        $this->comment = htmlspecialchars($this->comment);

        $this->shippingAddress = new OrderAddress($info);

        if (isset($info['sku']) && isset($info['qty'])) {
            $this->items[] = new OrderItem($info);
        }
    }

    public function addItem($item)
    {
        $this->items[] = new OrderItem($item);
    }

    public function addItems($items)
    {
        foreach ($items as $item) {
            $this->items[] = new OrderItem($item);
        }
    }

    public function setAddress($address)
    {
        $this->shippingAddress = new OrderAddress($address);
    }
}
