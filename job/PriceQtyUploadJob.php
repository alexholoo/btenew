<?php

include 'classes/Job.php';

class PriceQtyUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> Uploading ' . get_class($job));
            $job->download();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all priceqty uploaders 
        include_once('priceqty/upload/Base.php');
        include_once('priceqty/Filenames.php');

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

include __DIR__ . '/../public/init.php';

$job = new PriceQtyUploadJob();
$job->run($argv);
