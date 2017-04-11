<?php

include __DIR__ . '/../public/init.php';

class NeweggListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/Newegg_Listing_Importer.php");

        $job->run();
    }
}

$job = new NeweggListingImportJob();
$job->run($argv);
