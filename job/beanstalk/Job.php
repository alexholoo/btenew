<?php

use Phalcon\Di;

abstract class Job
{
    public function __construct()
    {
        $this->di = Di::getDefault();
        $this->db = $this->di->get('db');
    }
}
