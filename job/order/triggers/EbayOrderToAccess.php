<?php

class EbayOrderToAccess extends OrderTrigger
{
    protected $priority = 120;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'eBay-ODO|eBay-GFS') continue;
        // if (isset($order['overstock-changed']))
    }
}
