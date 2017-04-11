<?php

include __DIR__ . '/../public/init.php';

class BestbuyShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Bestbuy_Tracking_Exporter.php");

        $job->run();
    }
}

$job = new BestbuyShipmentExportJob();
$job->run($argv);
