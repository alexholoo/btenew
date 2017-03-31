<?php

include __DIR__ . '/../public/init.php';

class TrackingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getDownloadJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->download();
        }
    }

    protected function getDownloadJobs()
    {
        $jobs = [];

        // base class for all tracking downloaders
        include_once('tracking/download/Base.php');

        foreach (glob("tracking/download/*.php") as $filename) {
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

$job = new TrackingDownloadJob();
$job->run($argv);
