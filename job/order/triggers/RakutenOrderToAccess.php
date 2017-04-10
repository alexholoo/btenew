<?php

class RakutenOrderToAccess extends OrderTrigger
{
    protected $priority = 140;  // 0 to disable

    public function run($argv = [])
    {
        return;
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'Rakuten-BUY') continue;
        // if (isset($order['overstock-changed']))
    }
}
