<?php

namespace Supplier\Model;

use Utility\Arr;

class Order
{
    public $channel;
    public $date;
    public $orderId;
    public $express;
    public $contact;
    public $address;
    public $city;
    public $province;
    public $zipcode;
    public $country;
    public $phone;
    public $email;
    public $sku;
    public $price;
    public $qty;
    public $shipping;   // shipping fee

    public $customerPO;
    public $branch;     // the warehouse ship from
    public $comment;

    public function __construct($order)
    {
        $this->channel  = Arr::get($order, 'channel');
        $this->date     = Arr::get($order, 'date');
        $this->orderId  = Arr::get($order, 'orderId');
        $this->express  = Arr::get($order, 'express');
        $this->contact  = Arr::get($order, 'buyer'); // !
        $this->address  = Arr::get($order, 'address');
        $this->city     = Arr::get($order, 'city');
        $this->province = Arr::get($order, 'province');
        $this->zipcode  = Arr::get($order, 'postalcode'); // !
        $this->country  = Arr::get($order, 'country');
        $this->phone    = Arr::get($order, 'phone');
        $this->email    = Arr::get($order, 'email');
        $this->sku      = Arr::get($order, 'sku');
        $this->price    = Arr::get($order, 'price');
        $this->qty      = Arr::get($order, 'qty');
        $this->shipping = Arr::get($order, 'shipping');

        $this->branch     = Arr::get($order, 'branch');
        $this->comment    = Arr::get($order, 'comment');
        $this->customerPO = Arr::get($order, 'customerPO');
    }
}
