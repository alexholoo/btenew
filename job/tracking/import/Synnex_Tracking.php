<?php

class Synnex_Tracking extends TrackingImporter
{
    public function import()
    {
        $this->importFiles();
    }

    protected function importFile($xml)
    {
        //$xml = simplexml_load_file($filename);

        foreach ($xml->ShipNotice3D as $request) {

            $orderId = $request->CustomerPONumber;
            if (!$orderId) {
                $orderId = $request->PONumber;
            }

            if ($orderId) {
                $synShipDate = '20'.$request->ShipDate;
                $shipDate = date ('Y-m-d' ,strtotime($synShipDate));
                $carrierCode = $request->ShipCode;

                if (in_array($carrierCode, ['LMG', 'L18G', 'L18A'])) {
                    $carrierCode = "Loomis";
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

                $this->saveToDb([
                    'orderId'        => trim(strval($orderId)),
                    'shipDate'       => $shipDate,
                    'carrier'        => $carrierCode,
                    'shipMethod'     => '',
                    'trackingNumber' => trim(strval($trackingNumber)),
                    'sender'         => 'SYN-DS',
                ]);
            }
        }
    }

    protected function importFiles()
    {
        $filename = 'E:/BTE/tracking/synnex/synnex-master-shipment.xml';
        $out = fopen($filename, 'w');

        fwrite($out, '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>');
        fwrite($out, "\n");
        fwrite($out, "<SynnexB2B>\n");

       #$today = date('Ymd');
       #$files = glob("W:/data/csv/amazon/synnex-tracking/$today*_BTE_COMPUTER_856.xml");
       #$files = glob('w:/data/csv/amazon/synnex-tracking/*_BTE_COMPUTER_856.xml');

        $files = glob('E:/BTE/tracking/synnex/*_BTE_COMPUTER_856.xml');
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
