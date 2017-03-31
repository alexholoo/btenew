<?php

include 'classes/Job.php';

class eBayOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/import/Ebay_Order_Importer.php");

        $job->import();
    }
}

include __DIR__ . '/../public/init.php';

$job = new eBayOrderImportJob();
$job->run($argv);
