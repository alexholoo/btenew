<?php

namespace Supplier\Model;

use Supplier\Model\Response;

abstract class PriceAvailabilityResponse extends Response
{
    /**
     * @var arary
     *
     * $item = [
     *     'sku'   => '...',
     *     'price' => '...',
     *     'avail' => [
     *         [ 'branch' => 'BRANCH-1', 'qty' => 1 ],
     *         [ 'branch' => 'BRANCH-1', 'qty' => 2 ],
     *         [ 'branch' => 'BRANCH-1', 'qty' => 3 ],
     *     ]
     * ];
     */
    protected $items = array();

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items[0];
    }
}
