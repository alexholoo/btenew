<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrder extends Model
{
    public $Channel;
    public $OrderId;
    public $PurchaseDate;
    public $LastUpdateDate;
    public $OrderStatus;
    public $FulfillmentChannel;
    public $SalesChannel;
    public $ShipServiceLevel;
    public $CurrencyCode;
    public $OrderTotalAmount;
    public $NumberOfItemsShipped;
    public $NumberOfItemsUnshipped;
    public $PaymentMethod;
    public $BuyerName;
    public $BuyerEmail;
    public $ShipmentServiceLevelCategory;
    public $ShippedByAmazonTFM;
    public $OrderType;
    public $EarliestShipDate;
    public $LatestShipDate;
    public $EarliestDeliveryDate;
    public $LatestDeliveryDate;
    public $IsBusinessOrder;
    public $IsPrime;
    public $IsPremiumOrder;

    public function initialize()
    {
        $this->setSource("amazon_order");
        // TODO: $this->hasOne(AmazonOrderItem)
        // TODO: $this->hasOne(AmazonOrderShippingAddress)
    }
}
