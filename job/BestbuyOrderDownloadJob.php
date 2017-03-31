<?php

include 'classes/Job.php';

class BestbuyOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Bestbuy_Order_Downloader.php");

        $job->download();
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyOrderDownloadJob();
$job->run($argv);
