<?php

class AmazonOrderToAccess extends OrderTrigger
{
    protected $priority = 100;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'Amazon-ACA|Amazon-US') continue;
        // if (isset($order['overstock-changed']))
    }
}
