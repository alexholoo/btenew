<?php

use Phalcon\Di;

abstract class Job
{
    public function __construct()
    {
        $this->di = Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    protected function log($line)
    {
        static $first = true;

        $line = date('Y-m-d H:i:s '). $line ."\n";

        if ($first) {
            $first = false;
            $line = "\n". $line;
        }

        echo $line;

        $today = date('ymd');
        error_log($line, 3, APP_DIR . "/logs/job-$today.log");
    }

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }
}
