<?php

abstract class Job
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1');
    }

    protected function log($line)
    {
        static $first = true;

        $today = date('Y-m-d');
        $filename = APP_DIR . "/logs/job-$today.log";

        echo $line, "\n";

        if ($first) {
            $first = false;
            error_log("\n", 3, $filename);
        }

        $line = date('Y-m-d H:i:s '). $line ."\n";

        error_log($line, 3, $filename);
    }

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }
}
