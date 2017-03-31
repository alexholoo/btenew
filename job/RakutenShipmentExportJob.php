<?php

include 'classes/Job.php';

class RakutenShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Rakuten_Tracking_Exporter.php");

        $job->export();
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenShipmentExportJob();
$job->run($argv);
