<?php

include __DIR__ . '/../public/init.php';

class AmazonReportDownloadJob extends Job
{
    protected $store;
    protected $reportFolder = 'E:/BTE/amazon/reports/';

    protected $reportInfo;

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        /*
        Array
        (
            [_GET_ORDERS_DATA_] => Array
            (
                [reportType] => _GET_ORDERS_DATA_
                [fileCA] => OrdersData_ca.txt
                [fileUS] => OrdersData_us.txt
                [TTL] => 1 days
            )
            [ ... ] => Array( ... )
        )
        */
        $this->reportInfo = $this->getReportInfo();

        // CA
        $this->store = 'bte-amazon-ca';

        $list = $this->getReportRequestList('report_list_ca.csv');

        foreach ($list as $reportType => $reportId) {
            $reportFile = $this->reportInfo[$reportType]['fileCA'];
            $reportTTL  = $this->reportInfo[$reportType]['TTL'];
            $this->getReport($reportType, $reportId, $reportFile, $reportTTL);
        }

        // US
        $this->store = 'bte-amazon-us';

        $list = $this->getReportRequestList('report_list_us.csv');

        foreach ($list as $reportType => $reportId) {
            $reportFile = $this->reportInfo[$reportType]['fileUS'];
            $reportTTL  = $this->reportInfo[$reportType]['TTL'];
            $this->getReport($reportType, $reportId, $reportFile, $reportTTL);
        }
    }

    protected function getReportRequestList($reportFilename)
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
            //$this->log("File $reportFile is not too old");
            return;
        }

        $this->log("Downloading report: $reportType $reportId");

        $report = new \AmazonReport($this->store, $reportId);
        $report->fetchReport();
        $report->saveReport($reportFilename);
    }

    protected function getReportInfo()
    {
        $sql = 'SELECT report_type reportType, file_ca fileCA, file_us fileUS, ttl TTL FROM amazon_report_request';
        $result = $this->db->fetchAll($sql);

        return array_column($result, null, 'reportType');
    }
}

$job = new AmazonReportDownloadJob();
$job->run($argv);
