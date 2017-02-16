<?php

class TNT_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function merge()
    {
        $filename = 'w:/out/shipping/tntshipments.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            return;
        }

        while (($fields = fgetcsv($fp)) !== FALSE) {
            $shipmentStatus = $fields[11];
            if ($shipmentStatus == 'Shipped') {

                $orderId        = $fields[1];
                $orderItemId    = '';
                $quantity       = '';
                $shipDate       = date('Y-m-d', strtotime($fields[6]));
                $carrierCode    = 'Other';
                $carrierName    = 'TNT';
                $trackingNumber = $fields[0];
                $shipMethod     = 'Standard';

                $name           = $fields[5];
                $address        = '';
                $city           = '';
                $state          = '';
                $zip            = '';
                $country        = 'United States';
                $fullAddress    = "$name, $address, $city, $state, $zip, $country";
                $site           = 'United States';

                $this->log("\t$shipDate\t$orderId\t$trackingNumber");

                if ($this->amazonUSshipment) {
                    $row = [
                        $orderId,
                        $orderItemId,
                        $quantity,
                        $shipDate,
                        $carrierCode,
                        $carrierName,
                        $trackingNumber,
                        $shipMethod,
                       #$site
                    ];
                    $this->amazonUSshipment->write($row);
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

        fclose($fp);
    }
}
