<?php

use Supplier\DH\Ftp;

class DH_Tracking extends TrackingJob
{
    const TRACKING_FILE = 'E:/BTE/tracking/dh/DH-TRACKING';

    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function download()
    {
        Ftp::getTracking(self::TRACKING_FILE);
    }

    public function merge()
    {
        #$columns = [
        #    // 0,    1,             2,           3,              4,          5,
        #    [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
        #    [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
        #    [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        #];

        $filename = self::TRACKING_FILE;

        #if (gethostname() == 'BTELENOVO') {
        #    $filename = 'E:/BTE/tracking/dh/DH-TRACKING';
        #}

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        // import to dropship_tracking
        if (($fp = fopen($filename, 'r')) == false) {
            return;
        }

        while ($fields = fgetcsv($fp, 0, '|')) {
            if ($fields[0] == 'H1') {
                $fields = array_map('trim', $fields);

                $orderId = $fields[1];

                $fields = fgetcsv($fp, 0, '|');

                if ($fields[0] == 'D1') {
                    $fields = array_map('trim', $fields);

                    $trackingNumber = $fields[1];
                    $carrierCode = $fields[2]; //. ' ' .$fields[3];
                    $carrierName = ''; //$fields[4];
                    $shipDate = $fmtdate($fields[5]);
                }

                if ($trackingNumber) {
                    $orderItemId = '';
                    $quantity = '';
                    $shipMethod = 'DH_DS';
                    $fullAddress = '';
                    $site = 'Canada';

                    $this->log("\t$shipDate\t$orderId\t$trackingNumber");

                    if ($this->amazonCAshipment) {
                        $row = [
                            $orderId,
                            $orderItemId,
                            $quantity,
                            $shipDate,
                            $carrierCode,
                            $carrierName,
                            $trackingNumber,
                            $shipMethod,
                            $site
                        ];
                        $this->amazonCAshipment->write($row);
                    }

                    if ($this->masterShipment) {
                        $row = [
                            $orderId,
                            $orderItemId,
                            $quantity,
                            $shipDate,
                            $carrierCode,
                            $carrierName,
                            $trackingNumber,
                            $shipMethod,
                            $fullAddress,
                            $site
                        ];
                        $this->masterShipment->write($row);
                    }
                }
            }
        }

        fclose($fp);
    }
}
