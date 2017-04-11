<?php

include __DIR__ . '/../public/init.php';

class NewItemsUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getJobs('newitems/upload');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));
            $job->run();
        }
    }
}

$job = new NewItemsUploadJob();
$job->run($argv);
