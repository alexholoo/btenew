<?php

include __DIR__ . '/../public/init.php';

class BestbuyOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/import/Bestbuy_Order_Importer.php");

        $job->run();
    }
}

$job = new BestbuyOrderImportJob();
$job->run($argv);
