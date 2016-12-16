<?php

class DH_Tracking extends Job
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

        #$columns = [
        #    // 0,    1,             2,           3,              4,          5,
        #    [ 'H1', 'OrderNo',     'InvoiceNo', 'InvoiceTotal' ],
        #    [ 'D1', 'TrackingNum', 'Carrier',   'ServiceLevel', 'ShipMode', 'DateShipped' ],
        #    [ 'D2', 'ModelNo',     'Qty',       'SerialNo+',    'Price' ],
        #];

        $filename = 'w:/data/csv/DH-TRACKING';

        #if (gethostname() == 'BTELENOVO') {
        #    $filename = 'E:/BTE/shipping/DH-TRACKING';
        #}

        $fmtdate = function($str) {
            return substr($str, 4).'-'.substr($str, 0, 2).'-'.substr($str, 2, 2);
        };

        // import to dropship_tracking
        if (($fp = fopen($filename, 'r')) !== NULL) {
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
