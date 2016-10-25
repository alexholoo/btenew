<?php

class TrackingUploadJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
    }
}

include __DIR__ . '/../public/init.php';

$job = new TrackingUploadJob();
$job->run($argv);
