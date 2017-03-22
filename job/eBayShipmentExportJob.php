<?php

include 'classes/Job.php';

class eBayShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('tracking/export/Base.php');
        include_once('tracking/Filenames.php');

        $job = $this->getJob("tracking/export/eBay_Tracking.php");

        $job->export();
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

$job = new eBayShipmentExportJob();
$job->run($argv);
