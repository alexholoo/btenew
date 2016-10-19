<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrder extends Model
{
    public $id;
    public $orderID;
    public $extOrderID;
    public $status;
    public $buyerUsername;
    public $datePaid;
    public $currency;
    public $amountPaid;
    public $salesTaxAmount;
    public $shippingService;
    public $shippingServiceCost;
    public $name;
    public $address;
    public $address2;
    public $city;
    public $province;
    public $postalCode;
    public $country;
    public $phone;
    public $quantityPurchased;
    public $email;
    public $sku;
    public $transactionID;
    public $transactionPrice;
    public $tracking;
    public $itemID;
    public $recordNumber;
    
    public function getSource()
    {
        return 'ebay_order_report_bte';
    }

    public function initialize()
    {
    }
}
