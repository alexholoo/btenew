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
            $this->error(__METHOD__." File not found: $filename");
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
                    $carrierCode = $fields[2];
                    $carrierName = '';
                    $shipMethod  = $fields[3];
                    $shipDate = $fmtdate($fields[5]);

                    if ($trackingNumber) {
                        $this->saveToDb([
                            'orderId'        => $orderId,
                            'shipDate'       => $shipDate,
                            'carrierCode'    => $carrierCode,
                            'carrierName'    => $carrierName,
                            'shipMethod'     => $shipMethod,
                            'trackingNumber' => $trackingNumber,
                            'sender'         => 'DH-DS',
                        ]);
                    }
                }
            }
        }

        fclose($fp);
    }
}
