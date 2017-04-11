<?php

include __DIR__ . '/../public/init.php';

class TrackingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('tracking/import');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new TrackingImportJob();
$job->run($argv);
