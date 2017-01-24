<?php

include 'classes/Job.php';

class AmazonReportDownloadJob extends Job
{
    protected $store;
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    protected $reportTypes = [
        '_GET_MERCHANT_LISTINGS_DATA_LITER_',      #'.\out\MerchantListingsDataLiter_ca.txt',
        '_GET_AFN_INVENTORY_DATA_',                #'.\data\csv\amazon\FBACAD.txt',
        '_GET_ORDERS_DATA_',                       #'.\out\OrdersData_ca.txt',
        '_GET_MERCHANT_LISTINGS_DATA_',            #'.\data\csv\amazon\amazon_ca_listings.txt',
        '_GET_FLAT_FILE_ORDERS_DATA_',             #'.\data\csv\amazon\amazon_ca_order_report.txt',
        '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_',   #'.\data\csv\amazon\amazon_ca_FBA.txt',
        '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_',   #'.\data\csv\amazon\amazon_ca_unshipped.txt',
        '_GET_REFERRAL_FEE_PREVIEW_REPORT_',       #'.\data\csv\amazon\amazon-ca-referral-report.txt';
    ];

    protected $reportFilesCA = [
        'MerchantListingsDataLiter_ca.txt',
        'FBACAD.txt',
        'OrdersData_ca.txt',
        'amazon_ca_listings.txt',
        'amazon_ca_order_report.txt',
        'amazon_ca_FBA.txt',
        'amazon_ca_unshipped.txt',
        'amazon_ca_referral_report.txt',
    ];

    protected $reportFilesUS = [
        'MerchantListingsDataLiter_us.txt',
        'FBAUSA.txt',
        'OrdersData_us.txt',
        'amazon_us_listings.txt',
        'amazon_us_order_report.txt',
        'amazon_us_FBA.txt',
        'amazon_us_unshipped.txt',
        'amazon_us_referral_report.txt',
    ];

    protected $reportFreqs = [
        '1 days',
        '1 days',
        '1 days',
        '1 days',
        '10 minutes',
        '10 minutes',
        '10 minutes',
        '1 days',
    ];

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('Ymd');

        // CA
        $this->store = 'bte-amazon-ca';

        $list = $this->getReportRequestList();

        $type2File = array_combine($this->reportTypes, $this->reportFilesCA);

        foreach ($list as $reportType => $reportId) {
            $reportFile = $type2File[$reportType];
            $this->getReport($reportType, $reportId, $reportFile);
        }

        #$this->reportFilename = "E:/BTE/amazon/reports/amazon-ca-payment-$today.txt";
        #$this->getPaymentReport();

        // US
        $this->store = 'bte-amazon-us';

        #$list = $this->getReportRequestList();

        #$type2File = array_combine($this->reportTypes, $this->reportFilesUS);

        #foreach ($list as $reportType => $reportId) {
        #    $this->reportFilename = $type2File[$reportType];
        #    $this->getReport($reportType, $reportId, $reportFile);
        #}

        #$this->reportFilename = "E:/BTE/amazon/reports/amazon-us-payment-$today.txt";
        #$this->getPaymentReport();
    }

    protected function getReportRequestList()
    {
        $this->log('Requesting Amazon report list: '.$this->store);

        $reportList = new \AmazonReportRequestList($this->store);
        $reportList->setReportTypes($this->reportTypes);

        $reportList->setMaxCount(100);
        $reportList->fetchRequestList();

        $list = $reportList->getList();
        if (empty($list)) {
            $this->log('WHAT! empty report list: '.$this->store.' '.__METHOD__);
            return false;
        }

        $reportFilename = 'report_list.csv';

        if ($this->store == 'bte-amazon-ca') {
            $reportFilename = 'report_list_ca.csv';
        }

        if ($this->store == 'bte-amazon-us') {
            $reportFilename = 'report_list_us.csv';
        }

        $fp = fopen($this->reportFolder.$reportFilename, 'w');

        fputcsv($fp, [
           #'ReportRequestId',
            'ReportType',
           #'StartDate',
           #'EndDate',
           #'Scheduled',
            'SubmittedDate',
            'ReportProcessingStatus',
            'GeneratedReportId',
           #'StartedProcessingDate',
            'CompletedDate',
        ]);

        $newReports = [];

        foreach ($list as $item) {
            unset($item['ReportRequestId']);
            unset($item['StartDate']);
            unset($item['EndDate']);
            unset($item['Scheduled']);
            unset($item['StartedProcessingDate']);

            fputcsv($fp, $item);

            $reportType   = $item['ReportType'];
            $reportStatus = $item['ReportProcessingStatus'];
            $reportId     = $item['GeneratedReportId'];

            if ($reportStatus == '_DONE_' && !isset($newReports[$reportType])) {
                $newReports[$reportType] = $reportId;
            }
        }

        fclose($fp);

        return $newReports;
    }

    protected function getReport($reportType, $reportId, $reportFile)
    {
        $reportFilename = $this->reportFolder.$reportFile;

        $freqs = array_combine($this->reportTypes, $this->reportFreqs);
        $ttl = isset($freqs[$reportType]) ? $freqs[$reportType] : '1 days';

        if (file_exists($reportFilename) && time() < strtotime($ttl, filemtime($reportFilename))) {
            $this->log("File $reportFile is not too old");
            return;
        }

        $this->log("Downloading report: $reportType $reportId");

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($reportFilename);
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
        if (empty($list)) {
            return;
        }

        $reportId = $list[0]['ReportId'];

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($this->reportFilename);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonReportDownloadJob();
$job->run($argv);
