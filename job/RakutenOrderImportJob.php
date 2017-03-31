<?php

include 'classes/Job.php';

class RakutenOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/import/Rakuten_Order_Importer.php");

        $job->import();
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenOrderImportJob();
$job->run($argv);
