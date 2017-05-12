<?php

class BTEInventoryUpdate extends OrderTrigger
{
    protected $priority = 10;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->updateInventory();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function updateInventory()
    {
        if (count($this->orders) == 0) {
            return;
        }

        $inventoryService = $this->di->get('inventoryService');

        foreach ($this->orders as $order) {
            $channel = $order['channel'];
            $orderId = $order['order_id'];
            $sku     = $order['sku'];
            $qty     = $order['qty'];

            $parts = explode('-', $sku);
            $supplier = strtoupper($parts[0]);

            if ($supplier == 'BTE') {
                $row = $inventoryService->get($sku);

                if (!$row) {
                    $this->log("$channel $orderId $sku $qty: not found");
                    continue; // if not found, skip it
                }

                if (strtolower(trim($row['type'])) != 'self') {
                    $this->log("$channel $orderId $sku $qty: not 'self'");
                    continue;
                }

                $inventoryService->deduct($order);

                $this->log("$channel $orderId $sku $qty: deducted");
            }
        }

       #$this->log(count($this->orders). ' new orders');
    }
}
