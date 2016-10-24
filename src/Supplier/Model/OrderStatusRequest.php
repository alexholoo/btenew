<?php

namespace Supplier\Model;

use Supplier\Model\Request;

abstract class OrderStatusRequest extends Request
{
    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $invoice;

    /**
     * @param  string $orderId
     */
    public function setOrder($orderId, $invoice = '')
    {
        $this->orderId = $orderId;
        $this->invoice = $invoice;
    }
}
