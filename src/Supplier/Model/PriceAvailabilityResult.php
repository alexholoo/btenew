<?php

namespace Supplier\Model;

class PriceAvailabilityResult
{
    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var array of Supplier\Model\PriceAvailabilityItem
     */
    protected $items = [];

    /**
     * @param Supplier\Model\PriceAvailabilityItem $item
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * @return Supplier\Model\PriceAvailabilityItem
     */
    public function getFirst()
    {
        if (count($this->items) > 0) {
            return $this->items[0];
        }

        return new PriceAvailabilityItem();
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
