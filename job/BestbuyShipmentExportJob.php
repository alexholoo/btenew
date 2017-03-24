<?php

include 'classes/Job.php';

class BestbuyShipmentExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('tracking/export/Base.php');
        include_once('tracking/Filenames.php');

        $job = $this->getJob("tracking/export/Bestbuy_Tracking_Exporter.php");

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

$job = new BestbuyShipmentExportJob();
$job->run($argv);
