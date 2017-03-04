<?php

include 'classes/Job.php';

class ListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> Importing ' . get_class($job));
            $job->download();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all listing importers 
        include_once('listing/import/Base.php');
        include_once('listing/Filenames.php');

        foreach (glob("listing/import/*.php") as $filename) {
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

$job = new ListingImportJob();
$job->run($argv);
