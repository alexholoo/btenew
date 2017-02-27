<?php

class DH_Tracking extends TrackingImporter
{
    public function import()
    {
        #$columns = [
        #    // 0,    1,             2,           3,              4,          5,
        #    [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
        #    [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
        #    [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        #];

        $filename = Filenames::get('dh.tracking');

        #if (IS_PROD) {
        #    $filename = 'E:/BTE/tracking/dh/DH-TRACKING';
        #}

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        // import to dropship_tracking
        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

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

                // Amazon Report the Error: The carrier-code field contains an invalid value: Purolator.
                if ($carrierCode == 'Purolator') {
                    $carrierCode = 'Other';
                    $carrierName = 'Purolator';
                }

                // Amazon Report the Error: The carrier-code field contains an invalid value: Loomis.
                if ($carrierCode == 'Loomis') {
                    $carrierCode = 'Other';
                    $carrierName = 'Loomis';
                }

                if ($trackingNumber) {
                    $this->saveToDb([
                        'orderId'        => $orderId,
                        'shipDate'       => $shipDate,
                        'carrierCode'    => $carrierCode,
                        'carrierName'    => $carrierName,
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
