<?php

include 'classes/Job.php';

class NewItemsUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->upload();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all newitems uploaders
        include_once('newitems/upload/Base.php');
        include_once('newitems/Filenames.php');

        foreach (glob("newitems/upload/*.php") as $filename) {
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

$job = new NewItemsUploadJob();
$job->run($argv);
