<?php

include __DIR__ . '/../public/init.php';

class OrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getDownloadJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }

    protected function getDownloadJobs()
    {
        $jobs = [];

        // base class for all order downloaders
        include_once('order/download/Base.php');

        foreach (glob("order/download/*.php") as $filename) {
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

$job = new OrderDownloadJob();
$job->run($argv);
