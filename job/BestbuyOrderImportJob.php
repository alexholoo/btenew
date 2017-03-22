<?php

include 'classes/Job.php';

class BestbuyOrderImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('order/import/Base.php');
        include_once('order/Filenames.php');

        $job = $this->getJob("order/import/Bestbuy_Order.php");

        $job->import();
    }

    protected function getJob($filename)
    {
        include_once($filename);

        $path = pathinfo($filename);
        $class = $path['filename'];

        $job = false;

        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyOrderImportJob();
$job->run($argv);
