<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrder extends Model
{
    public $id;
    public $orderNumber;
    public $orderDateTime;
    public $salesChannel;
    public $fulfillmentOption;
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
    public $orderCustomerEmail;
    public $orderShippingMethod;
    public $itemSellerPartNo;
    public $itemNeweggNo;
    public $itemUnitPrice;
    public $extendUnitPrice;
    public $itemUnitShippingCharge;
    public $extendShippingCharge;
    public $orderShippingTotal;
    public $GSTorHSTTotal;
    public $PSTorQSTTotal;
    public $orderTotal;
    public $quantityOrdered;
    public $quantityShipped;
    public $shipDate;
    public $actualShippingCarrier;
    public $actualShippingMethod;
    public $trackingNumber;
    public $shipFromAddress;
    public $shipFromCity;
    public $shipFromState;
    public $shipFromZipcode;
    public $shipFromName;

    public function getSource()
    {
        return 'newegg_order_report';
    }

    public function initialize()
    {
    }
}
