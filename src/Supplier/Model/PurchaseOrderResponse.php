<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class PurchaseOrderResponse extends Response
{
    /**
     * @return Supplier\Model\PurchaseOrderResult
     */
    abstract public function parseXml();
}
