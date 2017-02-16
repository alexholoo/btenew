<?php

include 'classes/Job.php';

use Shipment\MasterShipmentFile;
use Shipment\AmazonShipmentFile;

class TrackingMergeJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $jobs = $this->getTrackingJobs();

        $master   = new MasterShipmentFile();
        $amazonCA = new AmazonShipmentFile('CA');
        $amazonUS = new AmazonShipmentFile('US');

        foreach ($jobs as $job) {
            $this->log('=> ' . get_class($job));

            $job->setMasterShipment($master);
            $job->setAmazonCAshipment($amazonCA);
            $job->setAmazonUSshipment($amazonUS);

            $job->download();
            $job->merge();
        }

        $master->compack();
    }

    protected function getTrackingJobs()
    {
        $jobs = [];

        // base class for all tracking job
        include_once('tracking/merge/Base.php');

        foreach (glob("tracking/merge/*.php") as $filename) {
            include_once($filename);

            $path = pathinfo($filename);
            $class = $path['filename'];

            if (class_exists($class)) {
                $job = new $class;
                $status = $job->getStatus();
                if ($status > 0) {
                    $jobs[] = $job;
                }
            }
        }

        return $jobs;
    }
}

include __DIR__ . '/../public/init.php';

$job = new TrackingMergeJob();
$job->run($argv);
