<?php

include 'classes/Job.php';

class RakutenOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Rakuten_Order_Downloader.php");

        $job->download();
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenOrderDownloadJob();
$job->run($argv);
