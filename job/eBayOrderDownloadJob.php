<?php

include 'classes/Job.php';

class eBayOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Ebay_Order_Downloader.php");

        $job->download();
    }
}

include __DIR__ . '/../public/init.php';

$job = new eBayOrderDownloadJob();
$job->run($argv);
