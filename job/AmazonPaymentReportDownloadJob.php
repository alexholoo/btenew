<?php

include 'classes/Job.php';

class AmazonPaymentReportDownloadJob extends Job
{
    protected $store;
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('Ymd');

        $this->store = 'bte-amazon-ca';
        $this->reportFilename = $this->reportFolder."amazon-ca-payment-$today.txt";
        $this->getPaymentReport();

        $this->store = 'bte-amazon-us';
        $this->reportFilename = $this->reportFolder."amazon-us-payment-$today.txt";
        $this->getPaymentReport();
    }

    protected function getPaymentReport()
    {
        if (file_exists($this->reportFilename)) {
            $this->log("File $this->reportFilename is already downloaded");
            return;
        }

        $this->log('Downloading Amazon payment report '. $this->store);

        $api = new \AmazonReportList($this->store);

        #$api->setTimeLimits('-24 hours');

        // Same thing is happening for all three types of settlement reports
        // _GET_V2_SETTLEMENT_REPORT_DATA_
        // _GET_ALT_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_
        // _GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_
        // _GET_PAYMENT_SETTLEMENT_DATA_

        $api->setReportTypes('_GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_');
        $api->setMaxCount(40);
        $api->fetchReportList();

        $list = $api->getList();
        //print_r($list);

        if (empty($list)) {
            $this->log('No payment report '. $this->store);
            return;
        }

        $reportId = $list[0]['ReportId'];

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($this->reportFilename);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonPaymentReportDownloadJob();
$job->run($argv);
