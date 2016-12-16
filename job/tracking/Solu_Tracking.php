<?php

class Solu_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function merge()
    {
        $filename = 'w:/out/shipping/solu_shipment.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            return;
        }

        $title = fgetcsv($fp);

        while (($fields = fgetcsv($fp))!== FALSE) {

            $orderId = str_replace(' ', '', $fields[13]);

            $trackingNumber = str_replace(' ', '', $fields[1]);
            $shipDate       = date('Y-m-d', strtotime($fields[2]));
            $carrierCode    = $fields[3]; // i.e. DHL
            $shipMethod     = $fields[4]; // i.e. Express
            $orderItemId    = '';
            $quantity       = '';
            $carrierName    = '';
            $fullAddress    = $fields[10];

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
                    'CA'
                ];
                $this->amazonCAshipment->write($row);
            }

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
                #   'US'
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
                    'Mixed'
                ];
                $this->masterShipment->write($row);
            }
        }

        fclose ($fp);
    }
}
