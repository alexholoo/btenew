<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrder extends Model
{
    public $channel;
    public $orderId;
    public $purchaseDate;
    public $lastUpdateDate;
    public $orderStatus;
    public $fulfillmentChannel;
    public $salesChannel;
    public $shipServiceLevel;
    public $currencyCode;
    public $orderTotalAmount;
    public $numberOfItemsShipped;
    public $numberOfItemsUnshipped;
    public $paymentMethod;
    public $buyerName;
    public $buyerEmail;
    public $shipmentServiceLevelCategory;
    public $shippedByAmazonTFM;
    public $orderType;
    public $earliestShipDate;
    public $latestShipDate;
    public $earliestDeliveryDate;
    public $latestDeliveryDate;
    public $isBusinessOrder;
    public $isPrime;
    public $isPremiumOrder;

    public function getSource()
    {
        return 'amazon_order';
    }

    public function initialize()
    {
        $this->hasMany('orderId', 'AmazonOrderItem', 'orderId')
        $this->hasOne('orderId', 'AmazonOrderShippingAddress', 'orderId')
    }
}
