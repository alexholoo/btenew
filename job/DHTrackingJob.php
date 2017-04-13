<?php

include __DIR__ . '/../public/init.php';

class DHTrackingJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/download/DH_Tracking_Downloader.php");
        $job->run();

        $job = $this->getJob("tracking/import/DH_Tracking_Importer.php");
        $job->run();
    }
}

$job = new DHTrackingJob();
$job->run($argv);
