<?php

class OutOfStockUpdate extends OrderTrigger
{
    protected $priority = 200;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->checkOutOfStock();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function checkOutOfStock()
    {
        $outOfStockItems = [];

        if (count($this->orders) > 0) {
            foreach ($this->orders as $key => $order) {
                $orderId = $order['order_id'];
                $sku     = $order['sku'];
                $qty     = $order['qty'];

                $this->log("Price & Availability for $sku of $orderId");

                $client = \Supplier\Supplier::createClient($sku);

                if ($client) {
                    $result = $client->getPriceAvailability($sku);
                    $availQty = $result->getFirst()->getTotalQty();

                    $this->log("\t$sku\t$availQty - $qty");

                    if ($availQty - $qty <= 0) {
                        $outOfStockItems[] = $sku;
                    }
                }
            }

            if ($outOfStockItems) {
                $this->updateOutOfStock($outOfStockItems);
            }
        }

       #$this->log(count($this->orders). ' new orders');
    }

    protected function updateOutOfStock($outOfStockItems)
    {
        $jobs = $this->getJobs('priceqty/export');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->setItems($outOfStockItems);
            $job->run();
        }

        $jobs = $this->getJobs('priceqty/upload');

        foreach ($jobs as $job) {
#           $this->log('=> ' . get_class($job));
#           $job->run();
        }
    }
}
