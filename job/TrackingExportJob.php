<?php

include 'classes/Job.php';

class TrackingExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getExportJobs();

        foreach ($jobs as $job) {
           #$this->log('=> Exporting ' . get_class($job));
            $job->export();
        }
    }

    protected function getExportJobs()
    {
        $jobs = [];

        // base class for all tracking exporters
        include_once('tracking/export/Base.php');
        include_once('tracking/Filenames.php');

        foreach (glob("tracking/export/*.php") as $filename) {
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

$job = new TrackingExportJob();
$job->run($argv);
