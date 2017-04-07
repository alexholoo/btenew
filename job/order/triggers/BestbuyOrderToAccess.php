<?php

class BestbuyOrderToAccess extends OrderTrigger
{
    protected $priority = 110;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'Bestbuy') continue;
        // if (isset($order['overstock-changed']))
    }
}
