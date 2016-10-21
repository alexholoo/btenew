<?php

namespace App\Models;

use Phalcon\Mvc\Model;

class NeweggOrder extends Model
{
    public $id;
    public $filename;
    public $orderNumber;
    public $orderDateTime;
    public $salesChannel;
    public $fulfillmentOption;
    public $orderCustomerEmail;
    public $orderShippingMethod;
    public $orderShippingTotal;
    public $GSTorHSTTotal;
    public $PSTorQSTTotal;
    public $orderTotal;

    public function getSource()
    {
        return 'newegg_order_report';
    }

    public function initialize()
    {
    }
}
