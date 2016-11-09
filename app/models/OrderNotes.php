<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class OrderNotes extends Model
{
    public $date;
    public $orderId;
    public $stockStatus;
    public $express;
    public $qty;
    public $supplier; // empty usually
    public $supplierSku;
    public $mpn;
    public $supplierNo; // empty usually
    public $notes;
    public $relatedSkus;
    public $dimension;

    public function getSource()
    {
        return 'ca_order_notes';
    }

    public function initialize()
    {
    }

    public function columnMap()
    {
        // Keys are the real names in the table and
        // the values their names in the application

        return array(
            'id'           => 'id',
            'date'         => 'date',
            'order_id'     => 'orderId',
            'stock_status' => 'stockStatus',
            'express'      => 'express',
            'qty'          => 'qty',
            'supplier'     => 'supplier',
            'supplier_sku' => 'supplierSku',
            'mpn'          => 'mpn',
            'supplier_no'  => 'supplierNo',
            'notes'        => 'notes',
            'related_sku'  => 'relatedSkus',
            'dimension'    => 'dimension',
        );
    }
}
