<?php

include 'classes/Job.php';

class BestbuyShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Bestbuy_Tracking_Exporter.php");

        $job->export();
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyShipmentExportJob();
$job->run($argv);
