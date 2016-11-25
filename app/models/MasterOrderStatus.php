<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class MasterOrderStatus extends Model
{
    public $orderItemId;
    public $date;
    public $channel;
    public $orderId;
    public $stockStatus;
    public $supplier;
    public $supplierSku;
    public $mfrpn;
    public $ponum;
    public $invoice;
    public $trackingNum;
    public $remarks;
    public $flag;
    public $relatedSku;
    public $dimension;

    public function getSource()
    {
        return 'master_order_status';
    }

    public function initialize()
    {
        $this->belongsTo("orderItemId", "App\\Models\\MasterOrderItem", "id");
        $this->belongsTo("orderId",     "App\\Models\\MasterOrder",     "id");
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'order_item_id' => 'orderItemId',
            'date'          => 'date',
            'channel'       => 'channel',
            'order_id'      => 'orderId',
            'stock_status'  => 'stockStatus',
            'supplier'      => 'supplier',
            'supplier_sku'  => 'supplierSku',
            'mfrpn'         => 'mfrpn',
            'ponum'         => 'ponum',
            'invoice'       => 'invoice',
            'trackingnum'   => 'trackingNum',
            'remarks'       => 'remarks',
            'flag'          => 'flag',
            'related_sku'   => 'relatedSku',
            'dimension'     => 'dimension',
        );
    }
}
