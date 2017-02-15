<?php

class DH_Tracking extends TrackingCollector
{
    public function collect()
    {
        #$columns = [
        #    // 0,    1,             2,           3,              4,          5,
        #    [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
        #    [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
        #    [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        #];

        $filename = 'W:/out/shipping/DH-TRACKING';

        #if (IS_PROD) {
        #    $filename = 'E:/BTE/tracking/dh/DH-TRACKING';
        #}

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        // import to dropship_tracking
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
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
                    $this->saveToDb([
                        'orderId'        => $orderId,
                        'shipDate'       => $shipDate,
                        'carrier'        => $carrierCode,
                        'shipMethod'     => '',
                        'trackingNumber' => $trackingNumber,
                        'sender'         => 'DH-DS',
                    ]);
                }
            }
        }

        fclose($fp);
    }
}
