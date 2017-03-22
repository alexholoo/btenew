<?php

include 'classes/Job.php';

class NeweggListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('listing/import/Base.php');
        include_once('listing/Filenames.php');

        $job = $this->getJob("listing/import/Newegg_Listing.php");

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

$job = new NeweggListingImportJob();
$job->run($argv);