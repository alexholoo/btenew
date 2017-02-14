<?php

include 'classes/Job.php';

class AmazonRequestReportsJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $reportTypes = [
            '_GET_MERCHANT_LISTINGS_DATA_'            => '',
            '_GET_MERCHANT_LISTINGS_DATA_LITER_'      => '',
            '_GET_AFN_INVENTORY_DATA_'                => '',
            '_GET_FLAT_FILE_ORDERS_DATA_'             => '-7 days',
            '_GET_AMAZON_FULFILLED_SHIPMENTS_DATA_'   => '-7 days',
            '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_'   => '-7 days',
            '_GET_REFERRAL_FEE_PREVIEW_REPORT_'       => '-7 days',
            '_GET_FLAT_FILE_PAYMENT_SETTLEMENT_DATA_' => '-7 days',
            '_GET_FBA_ESTIMATED_FBA_FEES_TXT_DATA_'   => '-30 days',
        ];

        foreach ($reportTypes as $reportType => $startDate) {
            $store = 'bte-amazon-ca';
            $this->requestReport($store, $reportType, $startDate);

            $store = 'bte-amazon-us';
            $this->requestReport($store, $reportType, $startDate);
        }
    }

    private function requestReport($store, $reportType, $startDate)
    {
        $this->log("Requesting report: $store $reportType");

        $api = new AmazonReportRequest($store);
        $api->setReportType($reportType);
        if ($startDate) {
            $api->setTimeLimits($startDate, 'now');
        }
        $api->requestReport();

        $this->log(print_r($api->getResponse(), true));
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonRequestReportsJob();
$job->run($argv);
