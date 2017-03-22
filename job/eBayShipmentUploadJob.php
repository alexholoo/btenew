<?php

include 'classes/Job.php';

class eBayShipmentUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('tracking/upload/Base.php');
        include_once('tracking/Filenames.php');

        $job = $this->getJob("tracking/upload/eBay_Shipment.php");

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

$job = new eBayShipmentUploadJob();
$job->run($argv);
