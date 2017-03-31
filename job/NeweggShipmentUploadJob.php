<?php

include 'classes/Job.php';

class NeweggShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/upload/Newegg_Shipment_Uploader.php");

        $job->upload();
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggShipmentUploadJob();
$job->run($argv);
