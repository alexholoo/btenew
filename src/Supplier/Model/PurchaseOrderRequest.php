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
     * @param Supplier\Model\Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        if (count($this->order->items) > 1) {
            return 'MULTI-ITEMS';
        }

        return $this->order->items[0]->sku;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order->orderId;
    }
}
