<?php

include 'classes/Job.php';

class NeweggShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/export/Newegg_Tracking_Exporter.php");

        $job->export();
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggShipmentExportJob();
$job->run($argv);

