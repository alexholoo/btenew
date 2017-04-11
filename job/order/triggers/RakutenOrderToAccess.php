<?php

class RakutenOrderToAccess extends OrderTrigger
{
    protected $priority = 140;  // 0 to disable

    public function run($argv = [])
    {
        return;

        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importRakutenOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function importRakutenOrders()
    {
        // if ($order['channel'] != 'Rakuten-BUY') continue;
        // if (isset($order['overstock-changed']))
    }
}
