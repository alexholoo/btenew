<?php

trait JobTrait
{
    protected $di;
    protected $db;
    protected $queue;
    protected $redis;

    protected function init()
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

        $line = date('Y-m-d H:i:s '). $line ."\n";

        if ($first) {
            $first = false;
            $line = "\n". $line;
        }

        echo $line;

        error_log($line, 3, APP_DIR . '/logs/job.log');
    }

    protected function elapsed($start)
    {
        return number_format(microtime(true) - $start, 4);
    }
}
