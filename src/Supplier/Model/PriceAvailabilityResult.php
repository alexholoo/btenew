<?php

namepspace Supplier\Model;

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
     * @var array of Supplier\PriceAvailabilityItem
     */
    protected $items = [];

    /**
     * @param Supplier\PriceAvailabilityItem $item
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * @return Supplier\PriceAvailabilityItem
     */
    public function getFirst()
    {
        if (count($this->items) > 0) {
            return $this->items[0];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
}
