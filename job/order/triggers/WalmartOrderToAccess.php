<?php

class WalmartOrderToAccess extends OrderTrigger
{
    protected $priority = 150;  // 0 to disable

    public function run($argv = [])
    {
        return;

        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importWalmartOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function importWalmartOrders()
    {
        // if ($order['channel'] != 'Walmart') continue;
        // if (isset($order['overstock-changed']))
    }
}
