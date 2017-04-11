<?php

include __DIR__ . '/../public/init.php';

class eBayOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Ebay_Order_Downloader.php");

        $job->run();
    }
}

$job = new eBayOrderDownloadJob();
$job->run($argv);
