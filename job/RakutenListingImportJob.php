<?php

include 'classes/Job.php';

class RakutenListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('listing/import/Base.php');

        $job = $this->getJob("listing/import/Rakuten_Listing_Importer.php");

        $job->import();
    }

    protected function getJob($filename)
    {
        include_once($filename);

        $path = pathinfo($filename);
        $class = $path['filename'];

        $job = false;

        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenListingImportJob();
$job->run($argv);
