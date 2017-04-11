<?php

include __DIR__ . '/../public/init.php';

class TrackingExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('tracking/export');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new TrackingExportJob();
$job->run($argv);
