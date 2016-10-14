<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class AmazonOrderItem extends Model
{
    public $OrderId;
    public $ASIN;
    public $SellerSKU;
    public $OrderItemId;
    public $Title;
    public $QuantityOrdered;
    public $QuantityShipped;
    public $CurrencyCode;
    public $ItemPrice;
    public $ShippingPrice;
    public $GiftWrapPrice;
    public $ItemTax;
    public $ShippingTax;
    public $GiftWrapTax;
    public $ShippingDiscount;
    public $PromotionDiscount;
    public $ConditionId;
    public $ConditionSubtypeId;
    public $ConditionNote;

    public function initialize()
    {
        $this->setSource("amazon_order_item");
    }
}
