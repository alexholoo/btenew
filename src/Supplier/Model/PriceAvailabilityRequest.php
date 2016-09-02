<?php

namespace Supplier\Model;

use Supplier\Model\Request;

abstract class PriceAvailabilityRequest extends Request
{
    /**
     * @var array
     */
    protected $partnums = array();

    /**
     * @param  string $partnum
     */
    public function addPartnum($partnum)
    {
        $this->partnums[] = $partnum;
    }

    /**
     * @return string
     */
    public function getPartnum()
    {
        return $this->partnums[0];
    }
}
