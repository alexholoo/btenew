<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class PurchaeOrderResponse extends Response
{
    /**
     * @var string
     */
    protected $orderNo;

    /**
     * @return string
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }
}
