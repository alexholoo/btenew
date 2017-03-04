<?php

include 'classes/Job.php';

class NewItemsExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> Exporting ' . get_class($job));
            $job->download();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all newitems exporters 
        include_once('newitems/export/Base.php');
        include_once('newitems/Filenames.php');

        foreach (glob("newitems/export/*.php") as $filename) {
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

$job = new NewItemsExportJob();
$job->run($argv);
