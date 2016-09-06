<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class PriceAvailabilityResponse extends Response
{
    /**
     * @return Supplier\Model\PriceAvailabilityResult
     */
    abstract public function parseXml();
}
