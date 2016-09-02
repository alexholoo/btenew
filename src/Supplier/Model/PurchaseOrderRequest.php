<?php

namespace Supplier\Model;

use Supplier\Model\Request;

abstract class PurchaseOrderRequest extends Request
{
    /**
     * @var Supplier\Model\Order
     */
    protected $order;

    /**
     * @param Supplier\Model\Order $order
     */
    public function addOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->order->sku;
    }
}
