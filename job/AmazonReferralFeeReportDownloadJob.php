<?php

include __DIR__ . '/../public/init.php';

class AmazonReferralFeeReportDownloadJob extends Job
{
    protected $store;
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->store = 'bte-amazon-ca';
        $this->reportFilename = $this->reportFolder."amazon_ca_referral_report.txt";
        $this->getReferralFeeReport();

        $this->store = 'bte-amazon-us';
        $this->reportFilename = $this->reportFolder."amazon_us_referral_report.txt";
        $this->getReferralFeeReport();
    }

    protected function getReferralFeeReport()
    {
        if (file_exists($this->reportFilename) && time() < strtotime('1 days', filemtime($this->reportFilename))) {
            //$this->log("File $this->reportFilename is not too old");
            return;
        }

        $this->log('Downloading referral fee report '.$this->store);

        $api = new \AmazonReportList($this->store);

        $api->setReportTypes('_GET_REFERRAL_FEE_PREVIEW_REPORT_');
        $api->setMaxCount(40);
        $api->fetchReportList();

        $list = $api->getList();
        //print_r($list);

        if (empty($list)) {
            $this->log('No referral fee report '.$this->store);
            return;
        }

        /**
         * $list = Array
         * (
         *     [0] => Array
         *     (
         *         [ReportId] => 4045933557017190
         *         [ReportType] => _GET_REFERRAL_FEE_PREVIEW_REPORT_
         *         [ReportRequestId] => 346955017190
         *         [AvailableDate] => 2017-01-24T23:19:42+00:00
         *         [Acknowledged] => false
         *     )
         *     [1] => Array(...)
         * )
         */

        $reportId = $list[0]['ReportId'];

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($this->reportFilename);
    }
}

$job = new AmazonReferralFeeReportDownloadJob();
$job->run($argv);
