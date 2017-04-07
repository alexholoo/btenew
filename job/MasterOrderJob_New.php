<?php

include __DIR__ . '/../public/init.php';

class MasterOrderJob_New extends Job
{
    protected $newOrders;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/Master_Order_Merger.php");
        $job->run();

        $job = $this->getJob("order/Master_Order_Importer.php");
        $job->run();

        $this->newOrders = $job->getNewOrders();
        $this->log(count($this->newOrders). " new orders");

        $this->fireOrderTriggers();
    }

    protected function fireOrderTriggers()
    {
        if (count($this->newOrders) > 0) {

            $triggers = $this->getTriggers();

            foreach ($triggers as $trigger) {
                $trigger->setOrders($this->newOrders);
                echo get_class($trigger), EOL;
#               $trigger->run();
            }
        }
    }

    protected function getTriggers()
    {
        $triggers = [];

        // base class for all order triggers
        include_once('order/triggers/Base.php');

        foreach (glob("order/triggers/*.php") as $filename) {
            include_once $filename;

            $path = pathinfo($filename);
            $class = $path['filename'];

            if (class_exists($class)) {
                $trigger = new $class;

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

$job = new MasterOrderJob_New();
$job->run($argv);
