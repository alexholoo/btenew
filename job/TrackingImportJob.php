<?php

include 'classes/Job.php';

class TrackingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getTrackingJobs();

        foreach ($jobs as $job) {
           #$this->log('=> Importing ' . get_class($job));
            $job->import();
        }
    }

    protected function getTrackingJobs()
    {
        $jobs = [];

        // base class for all tracking importers
        include_once('tracking/import/Base.php');
        include_once('tracking/Filenames.php');

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

include __DIR__ . '/../public/init.php';

$job = new TrackingImportJob();
$job->run($argv);
