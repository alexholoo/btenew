<?php

include __DIR__ . '/../public/init.php';

class AmazonRequestReportsJob extends Job
{
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        /*
        Array
        (
            [_GET_AFN_INVENTORY_DATA_] => [
                'startDate' => ...,
                'fileCA' => ...,
                'fileUS' => ...,
                'TTL'    => ...,
            ],
        )
        */
        $reportList = $this->getReportRequestList();

        foreach ($reportList as $reportType => $info) {
            $store = 'bte-amazon-ca';
            $this->requestReport($store, $reportType, $info['startDate'], $info['fileCA'], $info['TTL']);

            $store = 'bte-amazon-us';
            $this->requestReport($store, $reportType, $info['startDate'], $info['fileUS'], $info['TTL']);
        }
    }

    private function requestReport($store, $reportType, $startDate, $reportFile, $ttl)
    {
        $reportFilename = $this->reportFolder.$reportFile;

        if (file_exists($reportFilename) && time() < strtotime($ttl, filemtime($reportFilename))) {
            //$this->log("File $reportFile is not too old");
            return;
        }

        $this->log("Requesting report: $store $reportType");

        $api = new AmazonReportRequest($store);
        $api->setReportType($reportType);
        if ($startDate) {
            $api->setTimeLimits($startDate, 'now');
        }
        $api->requestReport();

       #$this->log(print_r($api->getResponse(), true));
    }

    private function getReportRequestList()
    {
        $sql = 'SELECT report_type reportType, start_date startDate, file_ca fileCA, file_us fileUS, ttl TTL FROM amazon_report_request';
        $result = $this->db->fetchAll($sql);

        return array_column($result, null, 'reportType');
    }
}

$job = new AmazonRequestReportsJob();
$job->run($argv);
