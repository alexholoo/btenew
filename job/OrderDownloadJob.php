<?php

include __DIR__ . '/../public/init.php';

class OrderDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('order/download');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new OrderDownloadJob();
$job->run($argv);
