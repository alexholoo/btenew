<?php

class OrderTrigger extends Job
{
    protected $orders;
    protected $priority = 0;  // 0 to disable

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}
