<?php

include 'classes/Job.php';

class PricelistImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('pricelist/import');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new PricelistImportJob();
$job->run($argv);
