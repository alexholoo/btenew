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

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                     => 'id',
            'OrderNumber'            => 'orderNumber',
            'ItemSellerPartNo'       => 'itemSellerPartNo',
            'ItemNeweggNo'           => 'itemNeweggNo',
            'ItemUnitPrice'          => 'itemUnitPrice',
            'ExtendUnitPrice'        => 'extendUnitPrice',
            'ItemUnitShippingCharge' => 'itemUnitShippingCharge',
            'ExtendShippingCharge'   => 'extendShippingCharge',
            'QuantityOrdered'        => 'quantityOrdered',
            'QuantityShipped'        => 'quantityShipped',
            'ShipDate'               => 'shipDate',
            'ActualShippingCarrier'  => 'actualShippingCarrier',
            'ActualShippingMethod'   => 'actualShippingMethod',
            'TrackingNumber'         => 'trackingNumber',
            'ShipFromAddress'        => 'shipFromAddress',
            'ShipFromCity'           => 'shipFromCity',
            'ShipFromState'          => 'shipFromState',
            'ShipFromZipcode'        => 'shipFromZipcode',
            'ShipFromName'           => 'shipFromName',
        );
    }
}
