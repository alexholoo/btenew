<?php

namespace Supplier\Model;

use Supplier\Model\Order;
use Supplier\Model\Request;

abstract class PurchaseOrderRequest extends Request
{
    /**
     * @var Supplier\Model\Order
     */
    protected $order;

    /**
     * @param array $order
     */
    public function addOrder($order)
    {
        $this->order = new Order($order);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->order->sku;
    }
}
