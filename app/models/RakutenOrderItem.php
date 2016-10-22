<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class rakuten_order_item extends Model
{
    public $id;
    public $receiptID;
    public $receiptItemID;
    public $listingID;
    public $sku;
    public $referenceId;
    public $quantity;
    public $qtyShipped;
    public $qtyCancelled;
    public $title;
    public $price;
    public $productRev;

    public function getSource()
    {
        return 'rakuten_order_item';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'              => 'id',
            'Receipt_ID'      => 'receiptID',
            'Receipt_Item_ID' => 'receiptItemID',
            'ListingID'       => 'listingID',
            'Sku'             => 'sku',
            'ReferenceId'     => 'referenceId',
            'Quantity'        => 'quantity',
            'Qty_Shipped'     => 'qtyShipped',
            'Qty_Cancelled'   => 'qtyCancelled',
            'Title'           => 'title',
            'Price'           => 'price',
            'Product_Rev'     => 'productRev',
        );
    }
}
