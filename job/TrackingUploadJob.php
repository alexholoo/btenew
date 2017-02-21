<?php

include 'classes/Job.php';

class TrackingUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getUploadJobs();

        foreach ($jobs as $job) {
            $this->log('=> Uploading ' . get_class($job));
            $job->upload();
        }
    }

    protected function getUploadJobs()
    {
        $jobs = [];

        // base class for all tracking uploaders
        include_once('tracking/upload/Base.php');
        include_once('tracking/Filenames.php');

        foreach (glob("tracking/upload/*.php") as $filename) {
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

$job = new TrackingUploadJob();
$job->run($argv);
