<?php

include 'classes/Job.php';

class BestbuyPriceQtyUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include_once('priceqty/upload/Base.php');
        include_once('priceqty/Filenames.php');

        $job = $this->getJob("priceqty/upload/Bestbuy_PriceQty_Uploader.php");

        $job->upload();
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

$job = new BestbuyPriceQtyUploadJob();
$job->run($argv);