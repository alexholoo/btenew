<?php

include __DIR__ . '/../public/init.php';

class MasterOrderJob extends Job
{
    protected $newOrders;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        // merge all orderlists of all marketplaces into master_orders.csv
        $job = $this->getJob("order/Master_Order_Merger.php");
        $job->run();

        // import all orders in master_orders.csv into db, find new orders
        $job = $this->getJob("order/Master_Order_Importer.php");
        $job->run();

        // invoke all order triggers with new orders
        $this->newOrders = $job->getNewOrders();
        $this->log(count($this->newOrders). " new orders");

        $this->fireOrderTriggers();
    }

    protected function fireOrderTriggers()
    {
        if (count($this->newOrders) > 0) {
            $orders = $this->newOrders;
            $triggers = $this->getTriggers();

            foreach ($triggers as $trigger) {
                $trigger->setOrders($orders);
#               echo 'Order Trigger: ', get_class($trigger), EOL;
                $trigger->run();
                $orders = $trigger->getOrders();
            }
        }
    }

    protected function getTriggers()
    {
        $triggers = [];

        foreach (glob("order/triggers/*.php") as $filename) {
            $trigger = $this->getJob($filename);
            if ($trigger) {
                $priority = $trigger->getPriority();

                if ($priority > 0) {
                    $triggers[] = [
                        'priority' => $priority,
                        'trigger'  => $trigger,
                    ];
                }
            }
        }

        // Trigger with smaller priority runs first
        usort($triggers, function($a, $b) {
            return $a['priority'] > $b['priority'];
        });

        return array_column($triggers, 'trigger', 'priority');
    }
}

$job = new MasterOrderJob();
$job->run($argv);
