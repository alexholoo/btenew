<?php

class Master_Order_Merger extends Job
{
    public function run($argv = [])
    {
        $filename = Filenames::get('master.order');
        $masterFile = new Marketplace\MasterOrderList($filename);

        $jobs = $this->getJobs('order/merge');
        foreach ($jobs as $job) {
            $job->setMasterFile($masterFile);
            $job->run();
        }
    }
}
