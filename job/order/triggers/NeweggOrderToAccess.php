<?php

class NeweggOrderToAccess extends OrderTrigger
{
    protected $priority = 130;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        // if ($order['channel'] != 'NeweggCA|NeweggUSA') continue;
        // if (isset($order['overstock-changed']))
    }
}
