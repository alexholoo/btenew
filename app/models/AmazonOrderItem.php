<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrderItem extends Model
{
    public $orderId;
    public $asin;
    public $sellerSKU;
    public $orderItemId;
    public $title;
    public $quantityOrdered;
    public $quantityShipped;
    public $currencyCode;
    public $itemPrice;
    public $shippingPrice;
    public $giftWrapPrice;
    public $itemTax;
    public $shippingTax;
    public $giftWrapTax;
    public $shippingDiscount;
    public $promotionDiscount;
    public $conditionId;
    public $conditionSubtypeId;
    public $conditionNote;

    public function getSource()
    {
        return 'amazon_order_item';
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
            'OrderId'            => 'orderId',
            'ASIN'               => 'asin',
            'SellerSKU'          => 'sellerSKU',
            'OrderItemId'        => 'orderItemId',
            'Title'              => 'title',
            'QuantityOrdered'    => 'quantityOrdered',
            'QuantityShipped'    => 'quantityShipped',
            'CurrencyCode'       => 'currencyCode',
            'ItemPrice'          => 'itemPrice',
            'ShippingPrice'      => 'shippingPrice',
            'GiftWrapPrice'      => 'giftWrapPrice',
            'ItemTax'            => 'itemTax',
            'ShippingTax'        => 'shippingTax',
            'GiftWrapTax'        => 'giftWrapTax',
            'ShippingDiscount'   => 'shippingDiscount',
            'PromotionDiscount'  => 'promotionDiscount',
            'ConditionId'        => 'conditionId',
            'ConditionSubtypeId' => 'conditionSubtypeId',
            'ConditionNote'      => 'conditionNote',
        );
    }
}
