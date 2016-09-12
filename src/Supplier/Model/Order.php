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
    public $state;
    public $zipcode;
    public $postalcode;
    public $country;
    public $phone;
    public $email;
    public $sku;
    public $price;
    public $qty;
    public $shipping;   // shipping fee

    public $customerPO;
    public $endUserPO;
    public $branch;     // the warehouse ship from
    public $comment;

    public function __construct($order)
    {
        $this->channel    = Arr::val($order, 'channel');
        $this->date       = Arr::val($order, 'date');
        $this->orderId    = Arr::val($order, 'orderId');
        $this->express    = Arr::val($order, 'express');
        $this->contact    = Arr::val($order, 'buyer'); // !
        $this->address    = Arr::val($order, 'address');
        $this->city       = Arr::val($order, 'city');
        $this->province   = Arr::val($order, 'province');
        $this->state      = $this->province;
        $this->postalcode = Arr::val($order, 'postalcode');
        $this->zipcode    = $this->postalcode;
        $this->country    = Arr::val($order, 'country');
        $this->phone      = Arr::val($order, 'phone');
        $this->email      = Arr::val($order, 'email');
        $this->sku        = Arr::val($order, 'sku');
        $this->price      = Arr::val($order, 'price');
        $this->qty        = Arr::val($order, 'qty');
        $this->shipping   = Arr::val($order, 'shipping');

        $this->branch     = Arr::val($order, 'branch');
        $this->comment    = Arr::val($order, 'comment');
        $this->customerPO = Arr::val($order, 'customerPO');
        $this->endUserPO  = Arr::val($order, 'endUserPO');
    }
}
