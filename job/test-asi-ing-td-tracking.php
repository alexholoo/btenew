<?php

include __DIR__ . '/../public/init.php';

class TestJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("tracking/download/Techdata_Tracking_Downloader.php");
        $job->run();

        $job = $this->getJob("tracking/download/Ingram_Tracking_Downloader.php");
        $job->run();

        $job = $this->getJob("tracking/download/ASI_Tracking_Downloader.php");
        $job->run();
    }
}

$job = new TestJob();
$job->run($argv);
