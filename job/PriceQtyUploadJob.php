<?php

include __DIR__ . '/../public/init.php';

class PriceQtyUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getUploadJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }

    protected function getUploadJobs()
    {
        $jobs = [];

        // base class for all priceqty uploaders 
        include_once('priceqty/upload/Base.php');

        foreach (glob("priceqty/upload/*.php") as $filename) {
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

$job = new PriceQtyUploadJob();
$job->run($argv);
