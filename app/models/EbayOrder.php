<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrder extends Model
{
    public $id;
    public $orderID;
    public $status;
    public $buyerUsername;
    public $datePaid;
    public $currency;
    public $amountPaid;
    public $salesTaxAmount;
    public $shippingService;
    public $shippingServiceCost;
    
    public function getSource()
    {
        return 'ebay_order_report_bte';
    }

    public function initialize()
    {
        $this->hasOne("orderID", "EbayOrderShippingAddress", "orderID");
        $this->hasMany("orderID", "EbayOrderItem", "orderID");
    }
}
