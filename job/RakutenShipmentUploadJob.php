<?php

include 'classes/Job.php';

class RakutenShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Rakuten_Shipment_Uploader.php");

        $job->upload();
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenShipmentUploadJob();
$job->run($argv);
