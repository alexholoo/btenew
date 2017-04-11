<?php

class EbayOrderToAccess extends OrderTrigger
{
    protected $priority = 120;  // 0 to disable

    public function run($argv = [])
    {
        return;

        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importEbayOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function importEbayOrders()
    {
        // if ($order['channel'] != 'eBay-ODO|eBay-GFS') continue;
        // if (isset($order['overstock-changed']))
    }
}
