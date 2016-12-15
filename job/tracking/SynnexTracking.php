<?php

class SynnexTracking extends Job
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
        $this->mergeFiles();
    }

    protected function mergeFile($xml)
    {
        //$xml = simplexml_load_file($filename);

        foreach ($xml->ShipNotice3D as $request) {

            $orderId = $request->CustomerPONumber;
            if (!$orderId) {
                $orderId = $request->PONumber;
            }

            if ($orderId) {

                $orderItemId = '';
                $quantity = '';
                $synShipDate = '20'.$request->ShipDate;
                $shipDate = date ('Y-m-d' ,strtotime($synShipDate));
                $carrierCode = $request->ShipCode;
                $carrierName = '';

                if (in_array($carrierCode, ['LMG', 'L18G', 'L18A'])) {
                    $carrierCode = "Other";
                    $carrierName = "Loomis";
                }
                elseif ($carrierCode == 'UPG') {
                    $carrierCode = "UPS";
                }
                elseif (in_array($carrierCode, ['PUX', 'PUG'])) {
                    $carrierCode = "Purolator";
                }
                elseif ($carrierCode == 'FDXH') {
                    $carrierCode = "Fedex";
                }

                $trackingNumber = $request->ShipTrackNo;
                $shipMethod = 'DSSYN';

                $name    = $request->ShipTo->AddressName;
                $address = $request->ShipTo->AddressLine1;
                $city    = $request->ShipTo->City;
                $state   = $request->ShipTo->State;
                $zip     = $request->ShipTo->ZipCode;
                $country = 'Canada';

                $fullAddress = "$name, $address, $city, $state, $zip, $country";

                $site = $country;

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

                if ($this->amazonCAshipment) {
                    $this->amazonCAshipment->write($row);
                }

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

                if ($this->masterShipment) {
                    $this->masterShipment->write($row);
                }
            }
        }
    }

    protected function mergeFiles()
    {
        $filename = 'E:/BTE/tracking/synnex-master-shipment.xml';
        $out = fopen($filename, 'w');

        fwrite($out, '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>');
        fwrite($out, "\n");
        fwrite($out, "<SynnexB2B>\n");

       #$today = date('Ymd');
       #$files = glob("W:/data/csv/amazon/synnex-tracking/$today*_BTE_COMPUTER_856.xml");

        $files = glob('w:/data/csv/amazon/synnex-tracking/*_BTE_COMPUTER_856.xml');
        foreach($files as $file) {
            // echo $file, EOL;

            $xml = simplexml_load_file($file);

            $this->mergeFile($xml);

            $result = $xml->xpath('/SynnexB2B/ShipNotice3D');

            while(list(, $node) = each($result)) {
                fwrite($out, $node->asXML());
                fwrite($out, "\n\n");
            }
        }

        fwrite($out, "</SynnexB2B>\n");
        fclose($out);

        // return $filename;
    }
}
