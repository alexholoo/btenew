<?php

include 'classes/Job.php';

class eBayShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Ebay_Tracking_Exporter.php");

        $job->export();
    }
}

include __DIR__ . '/../public/init.php';

$job = new eBayShipmentExportJob();
$job->run($argv);
