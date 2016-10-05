<?php

namespace Supplier\Synnex;

use Toolkit\Utils;

class FreightQuoteResult
{
    protected $shipMethods = [];

    public function add($shipMethod)
    {
        // $shipMethod['Code']
        // $shipMethod['Description']
        // $shipMethod['ServiceLevel']
        // $shipMethod['Freight']

        $this->shipMethods[] = $shipMethod;
    }

    public function getShipMethods()
    {
        return $this->shipMethods;
    }
}
