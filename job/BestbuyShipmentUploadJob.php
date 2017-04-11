<?php

include __DIR__ . '/../public/init.php';

class BestbuyShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Bestbuy_Shipment_Uploader.php");

        $job->run();
    }
}

$job = new BestbuyShipmentUploadJob();
$job->run($argv);
