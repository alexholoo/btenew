<?php

include 'classes/Job.php';

class ShippingEasyImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->importShippingEasy();
    }

    protected function importShippingEasy()
    {
        $filename = 'w:/out/shipping/shippingeasy-shipping-report.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');

        // skip the first few lines
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);

        while ($fields = fgetcsv($fp)) {
            try {
                $success = $this->db->insertAsDict('shippingeasy', [
                    'ShipDate'                 => $fields[0],
                    'User'                     => $fields[1],
                    'OrderDate'                => $fields[2],
                    'OrderTotal'               => $fields[3],
                    'Store'                    => $fields[4],
                    'OrderNumber'              => $fields[5],
                    'ShipFrom'                 => $fields[6],
                    'ShipFromAddress'          => $fields[7],
                    'Recipient'                => $fields[8],
                    'RecipientBillingAddress'  => $fields[9],
                    'RecipientShippingAddress' => $fields[10],
                    'EmailAddress'             => $fields[11],
                    'Carrier'                  => $fields[12],
                    'RateProvider'             => $fields[13],
                    'ServiceType'              => $fields[14],
                    'PackageType'              => $fields[15],
                    'ConfirmationOption'       => $fields[16],
                    'Quantity'                 => $fields[17],
                    'WeightOZ'                 => $fields[18],
                    'Zone'                     => $fields[19],
                    'DestinationCountry'       => $fields[20],
                    'DestinationCity'          => $fields[21],
                    'DestinationStateProvince' => $fields[22],
                    'TrackingNumber'           => ltrim($fields[23], "'"),
                    'ShippingPaidByCustomer'   => $fields[24],
                    'PostageCost'              => $fields[25],
                    'InsuranceCost'            => $fields[26],
                    'TotalShippingCost'        => $fields[27],
                    'ShippingMargin'           => $fields[28],
                    'SKU'                      => $fields[29],
                    'ItemName'                 => $fields[30],
                ]);
            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }

        fclose($fp);
    }
}

include __DIR__ . '/../public/init.php';

$job = new ShippingEasyImportJob();
$job->run($argv);
