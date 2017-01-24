<?php

include 'classes/Job.php';

class AmazonReportDownloadJob extends Job
{
    protected $store;
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    protected $reportInfo = [
        '_GET_MERCHANT_LISTINGS_DATA_LITER_' => [
            'FileCA' => 'MerchantListingsDataLiter_ca.txt',
            'FileUS' => 'MerchantListingsDataLiter_us.txt',
            'TTL'    => '1 days',
        ],

        '_GET_AFN_INVENTORY_DATA_' => [
            'FileCA' => 'FBACAD.txt',
            'FileUS' => 'FBAUSA.txt',
            'TTL'    => '1 days',
        ],

        '_GET_ORDERS_DATA_' => [
            'FileCA' => 'OrdersData_ca.txt',
            'FileUS' => 'OrdersData_us.txt',
            'TTL'    => '1 days',
        ],

        '_GET_MERCHANT_LISTINGS_DATA_' => [
            'FileCA' => 'amazon_ca_listings.txt',
            'FileUS' => 'amazon_us_listings.txt',
            'TTL'    => '1 days',
        ],

        '_GET_FLAT_FILE_ORDERS_DATA_' => [
            'FileCA' => 'amazon_ca_order_report.txt',
            'FileUS' => 'amazon_us_order_report.txt',
            'TTL'    => '10 minutes',
        ],

        '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_' => [
            'FileCA' => 'amazon_ca_FBA.txt',
            'FileUS' => 'amazon_us_FBA.txt',
            'TTL'    => '10 minutes',
        ],

        '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_' => [
            'FileCA' => 'amazon_ca_unshipped.txt',
            'FileUS' => 'amazon_us_unshipped.txt',
            'TTL'    => '10 minutes',
        ],

        '_GET_REFERRAL_FEE_PREVIEW_REPORT_' => [
            'FileCA' => 'amazon_ca_referral_report.txt',
            'FileUS' => 'amazon_us_referral_report.txt',
            'TTL'    => '1 days',
        ],

        '_GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_' => [
            'FileCA' => 'amazon-ca-payment.txt',
            'FileUS' => 'amazon-us-payment.txt',
            'TTL'    => '1 days',
        ]
    ];

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        // CA
        $this->store = 'bte-amazon-ca';

        $list = $this->getReportRequestList();

        foreach ($list as $reportType => $reportId) {
            $reportFile = $this->reportInfo[$reportType]['FileCA'];
            $reportTTL  = $this->reportInfo[$reportType]['TTL'];
            $this->getReport($reportType, $reportId, $reportFile, $reportTTL);
        }

        // US
        $this->store = 'bte-amazon-us';

        $list = $this->getReportRequestList();

        foreach ($list as $reportType => $reportId) {
            $reportFile = $this->reportInfo[$reportType]['FileUS'];
            $reportTTL  = $this->reportInfo[$reportType]['TTL'];
            $this->getReport($reportType, $reportId, $reportFile, $reportTTL);
        }
    }

    protected function getReportRequestList()
    {
        $this->log('Requesting Amazon report list: '.$this->store);

        $reportList = new \AmazonReportRequestList($this->store);
        $reportList->setReportTypes(array_keys($this->reportInfo));

        $reportList->setMaxCount(100);
        $reportList->fetchRequestList();

        $list = $reportList->getList();
        if (empty($list)) {
            $this->log('WHAT! empty report list: '.$this->store.' '.__METHOD__);
            return [];
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

    protected function getReport($reportType, $reportId, $reportFile, $ttl)
    {
        $reportFilename = $this->reportFolder.$reportFile;

        if (file_exists($reportFilename) && time() < strtotime($ttl, filemtime($reportFilename))) {
            $this->log("File $reportFile is not too old");
            return;
        }

        $this->log("Downloading report: $reportType $reportId");

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($reportFilename);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonReportDownloadJob();
$job->run($argv);
