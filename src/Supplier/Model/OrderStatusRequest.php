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
     * @param  string $orderId
     */
    public function setOrder($orderId)
    {
        $this->orderId = $orderId;
    }
}
