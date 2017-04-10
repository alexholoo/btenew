<?php

include __DIR__ . '/../public/init.php';

class TestOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Amazon_Order_Downloader.php");

        $job->download();
    }
}

$job = new TestOrderDownloadJob();
$job->run($argv);
