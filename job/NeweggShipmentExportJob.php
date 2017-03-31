<?php

include __DIR__ . '/../public/init.php';

class NeweggShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Newegg_Tracking_Exporter.php");

        $job->export();
    }
}

$job = new NeweggShipmentExportJob();
$job->run($argv);

