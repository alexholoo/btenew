<?php

class WalmartOrderToAccess extends OrderTrigger
{
    protected $priority = 150;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'Walmart') continue;
        // if (isset($order['overstock-changed']))
    }
}
