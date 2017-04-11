<?php

include __DIR__ . '/../public/init.php';

class TrackingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('tracking/download');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new TrackingDownloadJob();
$job->run($argv);
