<?php

class WalmartOrderToAccess extends OrderTrigger
{
    protected $priority = 150;  // 0 to disable

    public function run($argv = [])
    {
        return;
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'Walmart') continue;
        // if (isset($order['overstock-changed']))
    }
}
