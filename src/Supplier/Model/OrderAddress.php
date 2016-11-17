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

    public function __construct($info)
    {
        $this->contact    = htmlspecialchars(Arr::val($info, 'buyer'));
        $this->address    = htmlspecialchars(Arr::val($info, 'address'));
        $this->city       = htmlspecialchars(Arr::val($info, 'city'));
        $this->province   = htmlspecialchars(Arr::val($info, 'province'));
        $this->postalcode = htmlspecialchars(Arr::val($info, 'postalcode'));
        $this->country    = htmlspecialchars(Arr::val($info, 'country'));
        $this->phone      = htmlspecialchars(Arr::val($info, 'phone'));
        $this->email      = htmlspecialchars(Arr::val($info, 'email'));

        $this->state      = $this->province;
        $this->zipcode    = $this->postalcode;

       #$this->state = CanadaProvince::nameToCode($this->state);
       #$this->province = CanadaProvince::nameToCode($this->province);
    }
}
