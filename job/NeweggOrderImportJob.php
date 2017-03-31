<?php

include __DIR__ . '/../public/init.php';

class NeweggOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/import/Newegg_Order_Importer.php");

        $job->import();
    }
}

$job = new NeweggOrderImportJob();
$job->run($argv);
