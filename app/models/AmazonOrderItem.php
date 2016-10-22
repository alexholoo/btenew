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
}
