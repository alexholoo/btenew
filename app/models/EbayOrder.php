<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrder extends Model
{
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
        $this->hasMany('orderID', 'App\\Models\\EbayOrderItem',            'orderID', ['alias' => 'items']);
        $this->hasOne('orderID',  'App\\Models\\EbayOrderShippingAddress', 'orderID', ['alias' => 'shippingAddress']);
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                  => 'id',
            'OrderID'             => 'orderID',
            'Status'              => 'status',
            'BuyerUsername'       => 'buyerUsername',
            'DatePaid'            => 'datePaid',
            'Currency'            => 'currency',
            'AmountPaid'          => 'amountPaid',
            'SalesTaxAmount'      => 'salesTaxAmount',
            'ShippingService'     => 'shippingService',
            'ShippingServiceCost' => 'shippingServiceCost',
        );
    }
}
