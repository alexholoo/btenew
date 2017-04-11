<?php

include __DIR__ . '/../public/init.php';

class PriceQtyUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('priceqty/upload');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new PriceQtyUploadJob();
$job->run($argv);
