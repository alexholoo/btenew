<?php

include __DIR__ . '/../public/init.php';

class NewItemsExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('newitems/export');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new NewItemsExportJob();
$job->run($argv);
