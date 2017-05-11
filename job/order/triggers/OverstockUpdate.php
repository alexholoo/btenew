<?php

class OverstockUpdate extends OrderTrigger
{
    protected $priority = 20;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->updateOverstock();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function updateOverstock()
    {
        $this->overstockService = $this->di->get('overstockService');

        if (count($this->orders) > 0) {
            foreach ($this->orders as $key => $order) {
                $channel = $order['channel'];
                $orderId = $order['order_id'];
                $skuOrig = $order['sku'];
                $qty     = $order['qty'];

                $sku = $this->findSku($skuOrig);
                if (!$sku) {
                    $this->log("$channel $orderId $skuOrig $qty Not overstocked");
                    continue; // skip it if not found
                }

                if ($skuOrig != $sku) {
                    $this->log("$orderId $skuOrig ==>> $sku in overstock");
                    // let other orderTriggers know alternative sku used
                    $this->orders[$key]['altSku'] = $sku;
                }

                if ($qty >= 10) {
                    $this->error(__METHOD__ . " $orderId $qty, qty too big, please check");
                }

                $this->overstockService->deduct($sku, $order);

                // let other orderTriggers know it's in-stock
                $this->orders[$key]['overstock-changed'] = true;

                $this->log("$channel $orderId $sku $qty => $remaining");
            }
        }

       #$this->log(count($this->orders). ' new orders');
    }

    protected function findSku($sku)
    {
        // first, check the given sku in overstock
        if ($this->overstockService->get($sku)) {
            return $sku;
        }

        // if not found, then check the skus in same group
        $list = $this->di->get('skuService')->getAltSkus($sku);

        if (count($list) == 0) {
           #$this->error(__METHOD__.": no alternative sku found for $sku");
            return false;
        }

        $found = [];

        foreach ($list as $pn) {
            if ($this->overstockService->get($pn)) {
                $found[] = $pn;
            }
        }

        if (count($found) == 0) {
            return false;
        }

        if (count($found) > 1) {
            $this->error("Duplicated SKUs in overstock: ".implode(' / ', $found));
        }

        return $found[0];
    }
}
