<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class OrderStatusResponse extends Response
{
    /**
     * @return Supplier\Model\OrderStatusResult
     */
    abstract public function parse();
}
