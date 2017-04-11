<?php

include __DIR__ . '/../public/init.php';

class eBayShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Ebay_Shipment_Uploader.php");

        $job->run();
    }
}

$job = new eBayShipmentUploadJob();
$job->run($argv);
