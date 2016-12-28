<?php

include 'classes/Job.php';

class AmazonReportDownloadJob extends Job
{
    protected $store;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('Ymd');

        $this->store = 'bte-amazon-ca';
        $this->reportFilename = "E:/BTE/amazon/reports/amazon-ca-payment-$today.txt";
        $this->getPaymentReport();

        $this->store = 'bte-amazon-us';
        $this->reportFilename = "E:/BTE/amazon/reports/amazon-us-payment-$today.txt";
        $this->getPaymentReport();
    }

    protected function getPaymentReport()
    {
        echo 'Downloading Amazon payment report', EOL;

        $reportList = new \AmazonReportList($this->store);

        #$reportList->setTimeLimits('-24 hours');

        // Same thing is happening for all three types of settlement reports
        // _GET_V2_SETTLEMENT_REPORT_DATA_
        // _GET_ALT_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_
        // _GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_
        // _GET_PAYMENT_SETTLEMENT_DATA_

        $reportList->setReportTypes('_GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_');
        $reportList->fetchReportList();

        $list = $reportList->getList();

        $reportId = $list[0]['ReportId'];

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($this->reportFilename);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonReportDownloadJob();
$job->run($argv);
