<?php

include __DIR__ . '/../public/init.php';

class RakutenShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Rakuten_Shipment_Uploader.php");

        $job->run();
    }
}

$job = new RakutenShipmentUploadJob();
$job->run($argv);
