<?php

include 'classes/Job.php';

class BestbuyShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('tracking/upload/Base.php');
        include_once('tracking/Filenames.php');

        $job = $this->getJob("tracking/upload/Bestbuy_Shipment_Uploader.php");

        $job->upload();
    }

    protected function getJob($filename)
    {
        include_once($filename);

        $path = pathinfo($filename);
        $class = $path['filename'];

        $job = false;

        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyShipmentUploadJob();
$job->run($argv);
