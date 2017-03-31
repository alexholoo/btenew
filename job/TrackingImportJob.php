<?php

include __DIR__ . '/../public/init.php';

class TrackingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getTrackingJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->import();
        }
    }

    protected function getTrackingJobs()
    {
        $jobs = [];

        // base class for all tracking importers
        include_once('tracking/import/Base.php');

        foreach (glob("tracking/import/*.php") as $filename) {
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

$job = new TrackingImportJob();
$job->run($argv);
