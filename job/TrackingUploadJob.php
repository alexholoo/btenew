<?php

include __DIR__ . '/../public/init.php';

class TrackingUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('tracking/upload');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new TrackingUploadJob();
$job->run($argv);
