<?php

class Master_Order_Merger extends Job
{
    public function run($argv = [])
    {
        $this->log('=> '. __CLASS__);

        $filename = Filenames::get('master.order');
        $masterFile = new Marketplace\MasterOrderList($filename);

        $jobs = $this->getJobs('order/merge');
        foreach ($jobs as $job) {
            $job->setMasterFile($masterFile);
            $job->run();
        }
    }
}
