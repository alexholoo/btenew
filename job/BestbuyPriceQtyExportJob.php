<?php

include __DIR__ . '/../public/init.php';

class BestbuyPriceQtyExportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("priceqty/export/Bestbuy_PriceQty_Exporter.php");

        $job->run();
    }
}

$job = new BestbuyPriceQtyExportJob();
$job->run($argv);
