<?php

include __DIR__ . '/../public/init.php';

class BestbuyPriceQtyUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("priceqty/upload/Bestbuy_PriceQty_Uploader.php");

        $job->upload();
    }
}

$job = new BestbuyPriceQtyUploadJob();
$job->run($argv);
