<?php

abstract class PriceQty_Exporter extends Job
{
    protected $items = [];

    public function setItems($items)
    {
        $this->items = $items;
    }

    abstract public function export();
}
