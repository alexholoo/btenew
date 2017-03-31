<?php

include __DIR__ . '/../public/init.php';

class PriceQtyExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->export();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all priceqty exporters 
        include_once('priceqty/export/Base.php');

        foreach (glob("priceqty/export/*.php") as $filename) {
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

$job = new PriceQtyExportJob();
$job->run($argv);
