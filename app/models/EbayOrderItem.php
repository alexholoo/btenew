<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrderItem extends Model
{
    public $id;
    public $orderID;
    public $sku;
    public $quantityPurchased;
    public $transactionID;
    public $transactionPrice;
    public $tracking;
    public $itemID;
    public $email;
    public $recordNumber;

    public function getSource()
    {
        return 'ebay_order_item';
    }

    public function initialize()
    {
    }
}
