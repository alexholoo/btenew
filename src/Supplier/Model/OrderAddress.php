<?php

namespace Supplier\Model;

use Toolkit\Arr;
use Toolkit\CanadaProvince;

class OrderAddress
{
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

    public function __construct($order)
    {
        $this->contact    = htmlspecialchars(Arr::val($order, 'buyer'));
        $this->address    = htmlspecialchars(Arr::val($order, 'address'));
        $this->city       = htmlspecialchars(Arr::val($order, 'city'));
        $this->province   = htmlspecialchars(Arr::val($order, 'province'));
        $this->postalcode = htmlspecialchars(Arr::val($order, 'postalcode'));
        $this->country    = htmlspecialchars(Arr::val($order, 'country'));
        $this->phone      = htmlspecialchars(Arr::val($order, 'phone'));
        $this->email      = htmlspecialchars(Arr::val($order, 'email'));

        $this->state      = $this->province;
        $this->zipcode    = $this->postalcode;

       #$this->state = CanadaProvince::nameToCode($this->state);
       #$this->province = CanadaProvince::nameToCode($this->province);
    }
}
