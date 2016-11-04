<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class EbayOrderItem extends Model
{
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

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'                => 'id',
            'OrderID'           => 'orderID',
            'SKU'               => 'sku',
            'QuantityPurchased' => 'quantityPurchased',
            'TransactionID'     => 'transactionID',
            'TransactionPrice'  => 'transactionPrice',
            'Tracking'          => 'tracking',
            'ItemID'            => 'itemID',
            'Email'             => 'email',
            'RecordNumber'      => 'recordNumber',
        );
    }
}
