<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class PurchaeOrderResponse extends Response
{
    /**
     * @return Supplier\Model\PurchaseOrderResult
     */
    abstract public function parseXml();
}
