<?php

include __DIR__ . '/../public/init.php';

class NeweggOrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("order/download/Newegg_Order_Downloader.php");

        $job->download();
    }
}

$job = new NeweggOrderDownloadJob();
$job->run($argv);
