<?php

include 'classes/Job.php';

class BestbuyPriceQtyExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("priceqty/export/Bestbuy_PriceQty_Exporter.php");

        $job->export();
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyPriceQtyExportJob();
$job->run($argv);
