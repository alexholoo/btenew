<?php

include __DIR__ . '/../public/init.php';

class eBayListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/eBay_Listing_Importer.php");

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

$job = new eBayListingImportJob();
$job->run($argv);
