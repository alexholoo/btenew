<?php

class TNT_Tracking_Importer extends Tracking_Importer
{
    public function run($argv = [])
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function import()
    {
        $filename = Filenames::get('tnt.tracking');

        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        while (($fields = fgetcsv($fp)) !== FALSE) {

            // TODO: fix the hard code
            $shipmentStatus = $fields[11];

            if ($shipmentStatus == 'Shipped') {

                $orderId        = $fields[1];
                $shipDate       = date('Y-m-d', strtotime($fields[6]));
                $carrierCode    = 'TNT';
                $carrierName    = '';
                $trackingNumber = $fields[0];
                $shipMethod     = 'Standard';

                $this->saveToDb([
                    'orderId'        => $orderId,
                    'shipDate'       => $shipDate,
                    'carrierCode'    => $carrierCode,
                    'carrierName'    => $carrierName,
                    'shipMethod'     => $shipMethod,
                    'trackingNumber' => $trackingNumber,
                    'sender'         => 'BTE',
                ]);
            }
        }

        fclose($fp);
    }
}
