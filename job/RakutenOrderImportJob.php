<?php

include __DIR__ . '/../public/init.php';

class RakutenOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/import/Rakuten_Order_Importer.php");

        $job->import();
    }
}

$job = new RakutenOrderImportJob();
$job->run($argv);
