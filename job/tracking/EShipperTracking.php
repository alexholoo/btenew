<?php

class EShipperTracking extends Job
{
    protected $masterShipment;
    protected $amazonCAshipment;
    protected $amazonUSshipment;

    public function getStatus()
    {
        return 1; // 1-enabled, 2-disabled
    }

    public function setAmazonCAshipment($amazonCAshipment)
    {
        $this->amazonCAshipment = $amazonCAshipment;
    }

    public function setAmazonUSshipment($amazonUSshipment)
    {
        $this->amazonUSshipment = $amazonUSshipment;
    }

    public function setMasterShipment($masterShipment)
    {
        $this->masterShipment = $masterShipment;
    }

    public function merge()
    {
        $this->log("=> ". __CLASS__);

        $filename = 'w:/out/shipping/eshipper_shipment.csv';

        if (($fp = fopen($filename, 'r')) == false) {
            return;
        }

        $title = fgetcsv($fp);

        while (($fields = fgetcsv($fp))!== FALSE) {

            $orderId        = $fields[2];
            $trackingNumber = $fields[22];
            $shipDate       = $fields[0];
            $carrier        = $fields[3];
            $shipMethod     = $fields[4];
            $orderItemId    = '';
            $quantity       = '';
            $carrierName    = '';
            $fullAddress    = implode(', ', array_slice($fields, 13, 6));

            if ($this->amazonCAshipment) {
                $row = [
                    $orderId,
                    $orderItemId,
                    $quantity,
                    $shipDate,
                    $carrier,
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
                    $carrier,
                    $carrierName,
                    $trackingNumber,
                    $shipMethod,
                   #'US'
                ];
                $this->amazonUSshipment->write($row);
            }

            if ($this->masterShipment) {
                $row = [
                    $orderId,
                    $orderItemId,
                    $quantity,
                    $shipDate,
                    $carrier,
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
