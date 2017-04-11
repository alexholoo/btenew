<?php

include __DIR__ . '/../public/init.php';

class PriceQtyExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('priceqty/export');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new PriceQtyExportJob();
$job->run($argv);
