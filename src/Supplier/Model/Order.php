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

    public function __construct($order)
    {
        $this->channel     = Arr::val($order, 'channel');
        $this->date        = Arr::val($order, 'date');
        $this->orderId     = Arr::val($order, 'orderId');
        $this->express     = Arr::val($order, 'express');
        $this->shipMethod  = Arr::val($order, 'shipMethod');
        $this->branch      = Arr::val($order, 'branch');
        $this->comment     = Arr::val($order, 'comment');
        $this->notifyEmail = Arr::val($order, 'notifyEmail');

        $this->comment = htmlspecialchars($this->comment);

        $this->shippingAddress = new OrderAddress($order);

        if (isset($order['sku']) && isset($order['qty'])) {
            $this->items[] = new OrderItem($order);
        }
    }

    public function setItems($items)
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
