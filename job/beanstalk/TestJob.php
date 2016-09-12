<?php

class TestJob extends Job
{
    public function run($args)
    {
        echo __METHOD__, ' ';
        print_r($args);
        echo PHP_EOL;
    }
}
