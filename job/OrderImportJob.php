<?php

include __DIR__ . '/../public/init.php';

class OrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getImportJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }

    protected function getImportJobs()
    {
        $jobs = [];

        // base class for all order importers
        include_once('order/import/Base.php');

        foreach (glob("order/import/*.php") as $filename) {
            include_once($filename);

            $path = pathinfo($filename);
            $class = $path['filename'];

            if (class_exists($class)) {
                $job = new $class;
                $jobs[] = $job;
            }
        }

        return $jobs;
    }
}

$job = new OrderImportJob();
$job->run($argv);
