<?php

// php jobrun.php TestJob

class TestJob extends Job
{
    public function run($args = [])
    {
        for ($i=0; $i<10; $i++) {
            echo __FILE__, ' ', __METHOD__, ' ', $i, EOL;
            sleep(1);
        }
    }
}
