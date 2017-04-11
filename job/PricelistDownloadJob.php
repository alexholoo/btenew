<?php

include 'classes/Job.php';

class PricelistDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('pricelist/download');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new PricelistDownloadJob();
$job->run($argv);
