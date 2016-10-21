<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrderItem extends Model
{
    public $id;
    public $orderNumber;
    public $itemSellerPartNo;
    public $itemNeweggNo;
    public $itemUnitPrice;
    public $extendUnitPrice;
    public $itemUnitShippingCharge;
    public $extendShippingCharge;
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
        return 'newegg_order_item';
    }

    public function initialize()
    {
    }
}
