<?php

abstract class OrderTrigger extends Job
{
    protected $orders;
    protected $priority = 0; // Trigger with smaller priority runs first
                             // 0 to disable the trigger

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}
