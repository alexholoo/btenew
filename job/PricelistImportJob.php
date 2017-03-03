<?php

include 'classes/Job.php';

class PricelistImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs();

        foreach ($jobs as $job) {
            $this->log('=> Importing ' . get_class($job));
            $job->import();
        }
    }

    protected function getJobs()
    {
        $jobs = [];

        // base class for all pricelist downloaders
        include_once('pricelist/import/Base.php');
        include_once('pricelist/Filenames.php');

        foreach (glob("pricelist/import/*.php") as $filename) {
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

$job = new PricelistImportJob();
$job->run($argv);
