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
        $this->hasMany('orderId', 'App\\Models\\AmazonOrderItem', 'orderId');
        $this->hasOne('orderId', 'App\\Models\\AmazonOrderShippingAddress', 'orderId');
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                           => 'id',
            'Channel'                      => 'channel',
            'OrderId'                      => 'orderId',
            'PurchaseDate'                 => 'purchaseDate',
            'LastUpdateDate'               => 'lastUpdateDate',
            'OrderStatus'                  => 'orderStatus',
            'FulfillmentChannel'           => 'fulfillmentChannel',
            'SalesChannel'                 => 'salesChannel',
            'ShipServiceLevel'             => 'shipServiceLevel',
            'CurrencyCode'                 => 'currencyCode',
            'OrderTotalAmount'             => 'orderTotalAmount',
            'NumberOfItemsShipped'         => 'numberOfItemsShipped',
            'NumberOfItemsUnshipped'       => 'numberOfItemsUnshipped',
            'PaymentMethod'                => 'paymentMethod',
            'BuyerName'                    => 'buyerName',
            'BuyerEmail'                   => 'buyerEmail',
            'ShipmentServiceLevelCategory' => 'shipmentServiceLevelCategory',
            'ShippedByAmazonTFM'           => 'shippedByAmazonTFM',
            'OrderType'                    => 'orderType',
            'EarliestShipDate'             => 'earliestShipDate',
            'LatestShipDate'               => 'latestShipDate',
            'EarliestDeliveryDate'         => 'earliestDeliveryDate',
            'LatestDeliveryDate'           => 'latestDeliveryDate',
            'IsBusinessOrder'              => 'isBusinessOrder',
            'IsPrime'                      => 'isPrime',
            'IsPremiumOrder'               => 'isPremiumOrder',
        );
    }
}
