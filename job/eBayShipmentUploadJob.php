<?php

include 'classes/Job.php';

class eBayShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Ebay_Shipment_Uploader.php");

        $job->upload();
    }
}

include __DIR__ . '/../public/init.php';

$job = new eBayShipmentUploadJob();
$job->run($argv);
