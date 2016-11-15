<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class BatchPurchaseResponse extends Response
{
    /**
     * @return Supplier\Model\BatchPurchaseResult
     */
    abstract public function parseXml();
}
