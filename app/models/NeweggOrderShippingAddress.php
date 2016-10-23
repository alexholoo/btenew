<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrderShippingAddress extends Model
{
    public $id;
    public $orderNumber;
    public $shipToAddressLine1;
    public $shipToAddressLine2;
    public $shipToCity;
    public $shipToState;
    public $shipToZipCode;
    public $shipToCountry;
    public $shipToFirstName;
    public $shipToLastName;
    public $shipToCompany;
    public $shipToPhoneNumber;

    public function getSource()
    {
        return 'newegg_order_shipping_address';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                 => 'id',
            'OrderNumber'        => 'orderNumber',
            'ShipToAddressLine1' => 'shipToAddressLine1',
            'ShipToAddressLine2' => 'shipToAddressLine2',
            'ShipToCity'         => 'shipToCity',
            'ShipToState'        => 'shipToState',
            'ShipToZipCode'      => 'shipToZipCode',
            'ShipToCountry'      => 'shipToCountry',
            'ShipToFirstName'    => 'shipToFirstName',
            'ShipToLastName'     => 'shipToLastName',
            'ShipToCompany'      => 'shipToCompany',
            'ShipToPhoneNumber'  => 'shipToPhoneNumber',
        );
    }
}
