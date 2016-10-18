<?php

class NeweggOrderJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $client = new Marketplace\Newegg\Client('CA');
        $client->getOrders();

        $folder = $client->getOrderFolder();
        $this->importOrders($folder);
    }

    private function importOrders($path)
    {
        $path = trim($path, '/');

        $files = glob("$path/OrderList_*.*");
        foreach ($files as $file) {
            $this->importOrderFile($file);
        }
    }

    private function importOrderFile($file)
    {
        if ($this->alreadyImported($file)) {
            return;
        }

        if (!($fh = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        fgetcsv($fh); // skip the first line

        $count = 0;

        while(($fields = fgetcsv($fh))) {

            $OrderNumber            = $fields[0];
            $OrderDateTime          = $fields[1];
            $SalesChannel           = $fields[2];
            $FulfillmentOption      = $fields[3];
            $ShipToAddressLine1     = $fields[4];
            $ShipToAddressLine2     = $fields[5];
            $ShipToCity             = $fields[6];
            $ShipToState            = $fields[7];
            $ShipToZipCode          = $fields[8];
            $ShipToCountry          = $fields[9];
            $ShipToFirstName        = $fields[10];
            $ShipToLastName         = $fields[11];
            $ShipToCompany          = $fields[12];
            $ShipToPhoneNumber      = $fields[13];
            $OrderCustomerEmail     = $fields[14];
            $OrderShippingMethod    = $fields[15];
            $ItemSellerPartNo       = $fields[16];
            $ItemNeweggNo           = $fields[17];
            $ItemUnitPrice          = $fields[18];
            $ExtendUnitPrice        = $fields[19];
            $ItemUnitShippingCharge = $fields[20];
            $ExtendShippingCharge   = $fields[21];
            $OrderShippingTotal     = $fields[22];
            $GSTorHSTTotal          = $fields[23];
            $PSTorQSTTotal          = $fields[24];
            $OrderTotal             = $fields[25];
            $QuantityOrdered        = $fields[26];
            $QuantityShipped        = $fields[27];
            $ShipDate               = $fields[28];
            $ActualShippingCarrier  = $fields[29];
            $ActualShippingMethod   = $fields[30];
            $TrackingNumber         = $fields[31];
            $ShipFromAddress        = $fields[32];
            $ShipFromCity           = $fields[33];
            $ShipFromState          = $fields[34];
            $ShipFromZipcode        = $fields[35];
            $ShipFromName           = $fields[36];

            $orderSeq = 1;
            $success = false;
            $OrderNumUniq = $OrderNumber;

            while (!$success) {
                try {
                    $success = $this->db->insertAsDict('newegg_order_report',
                        array(
                            'Filename'               => basename($file),
                            'OrderNumber'            => $OrderNumUniq,
                            'OrderDateTime'          => $OrderDateTime,
                            'SalesChannel'           => $SalesChannel,
                            'FulfillmentOption'      => $FulfillmentOption,
                            'ShipToAddressLine1'     => $ShipToAddressLine1,
                            'ShipToAddressLine2'     => $ShipToAddressLine2,
                            'ShipToCity'             => $ShipToCity,
                            'ShipToState'            => $ShipToState,
                            'ShipToZipCode'          => $ShipToZipCode,
                            'ShipToCountry'          => $ShipToCountry,
                            'ShipToFirstName'        => $ShipToFirstName,
                            'ShipToLastName'         => $ShipToLastName,
                            'ShipToCompany'          => $ShipToCompany,
                            'ShipToPhoneNumber'      => $ShipToPhoneNumber,
                            'OrderCustomerEmail'     => $OrderCustomerEmail,
                            'OrderShippingMethod'    => $OrderShippingMethod,
                            'ItemSellerPartNo'       => $ItemSellerPartNo,
                            'ItemNeweggNo'           => $ItemNeweggNo,
                            'ItemUnitPrice'          => $ItemUnitPrice,
                            'ExtendUnitPrice'        => $ExtendUnitPrice,
                            'ItemUnitShippingCharge' => $ItemUnitShippingCharge,
                            'ExtendShippingCharge'   => $ExtendShippingCharge,
                            'OrderShippingTotal'     => $OrderShippingTotal,
                            'GSTorHSTTotal'          => $GSTorHSTTotal,
                            'PSTorQSTTotal'          => $PSTorQSTTotal,
                            'OrderTotal'             => $OrderTotal,
                            'QuantityOrdered'        => $QuantityOrdered,
                            'QuantityShipped'        => $QuantityShipped,
                            'ShipDate'               => $ShipDate,
                            'ActualShippingCarrier'  => $ActualShippingCarrier,
                            'ActualShippingMethod'   => $ActualShippingMethod,
                            'TrackingNumber'         => $TrackingNumber,
                            'ShipFromAddress'        => $ShipFromAddress,
                            'ShipFromCity'           => $ShipFromCity,
                            'ShipFromState'          => $ShipFromState,
                            'ShipFromZipcode'        => $ShipFromZipcode,
                            'ShipFromName'           => $ShipFromName,
                        )
                    );

                    $count++;

                } catch (Exception $e) {
                    //echo $e->getMessage(), EOL;
                    $OrderNumUniq = $OrderNumber . '~' . $orderSeq++;
                    echo basename($file), ' - ', $OrderNumUniq, EOL;
                }
            }
        }

        fclose($fh);

        echo basename($file), ": $count orders imported\n";
    }

    private function alreadyImported($file)
    {
        $file = basename($file);
        $sql = "SELECT filename FROM newegg_order_report WHERE filename='$file'";
        $result = $this->db->fetchOne($sql);

        return $result;
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggOrderJob();
$job->run($argv);
