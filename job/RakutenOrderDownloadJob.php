<?php

include __DIR__ . '/../public/init.php';

class RakutenOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Rakuten_Order_Downloader.php");

        $job->download();
    }
}

$job = new RakutenOrderDownloadJob();
$job->run($argv);
