<?php

class TestJob
{
    public function run()
    {
        for ($i=0; $i<10; $i++) {
            echo __FILE__, ' ', __METHOD__, ' ', $i, PHP_EOL;
            sleep(1);
        }
    }
}

$job = new TestJob();
$job->run();