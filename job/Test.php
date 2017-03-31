<?php

include __DIR__ . '/../public/init.php';

class TestJob extends Job
{
    public function run($args = [])
    {
        for ($i=0; $i<10; $i++) {
            $this->log(__FILE__. ' ' .__METHOD__. ' ' .$i);
            $this->error(__FILE__. ' ' .__METHOD__. ' ' .$i);
            sleep(1);
        }
    }
}

$job = new TestJob();
$job->run($argv);
