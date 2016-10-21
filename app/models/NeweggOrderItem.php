<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrderItem extends Model
{
    public $id;
    public $OrderNumber;
    public $ItemSellerPartNo;
    public $ItemNeweggNo;
    public $ItemUnitPrice;
    public $ExtendUnitPrice;
    public $ItemUnitShippingCharge;
    public $ExtendShippingCharge;
    public $QuantityOrdered;
    public $QuantityShipped;
    public $ShipDate;
    public $ActualShippingCarrier;
    public $ActualShippingMethod;
    public $TrackingNumber;

    public function getSource()
    {
        return 'newegg_order_item';
    }

    public function initialize()
    {
    }
}
