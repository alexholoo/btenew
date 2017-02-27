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
                $carrierName = '';

                // Amazon Report the Error: The carrier-code field contains an invalid value: Purolator.
                // Amazon Report the Error: The carrier-code field contains an invalid value: Loomis.

                if (in_array($carrierCode, ['LMG', 'L18G', 'L18A'])) {
                    $carrierCode = "Other";
                    $carrierName = "Loomis";
                }
                elseif ($carrierCode == 'UPG') {
                    $carrierCode = "UPS";
                }
                elseif (in_array($carrierCode, ['PUX', 'PUG'])) {
                    $carrierCode = "Other";
                    $carrierName = "Purolator";
                }
                elseif ($carrierCode == 'FDXH') {
                    $carrierCode = "Fedex";
                }

                $trackingNumber = $request->ShipTrackNo;

                $this->saveToDb([
                    'orderId'        => strval($orderId),
                    'shipDate'       => $shipDate,
                    'carrierCode'    => $carrierCode,
                    'carrierName'    => $carrierName,
                    'shipMethod'     => '',
                    'trackingNumber' => strval($trackingNumber),
                    'sender'         => 'SYN-DS',
                ]);
            }
        }
    }

    protected function importFiles()
    {
        $folder = Filenames::get('synnex.tracking');

        $filename = "$folder/synnex-master-shipment.xml";
        $out = fopen($filename, 'w');

        fwrite($out, '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>');
        fwrite($out, "\n");
        fwrite($out, "<SynnexB2B>\n");

       #$today = date('Ymd');
       #$files = glob("$folder/$today*_BTE_COMPUTER_856.xml");
       #$files = glob("$folder/*_BTE_COMPUTER_856.xml");

        $files = glob("$folder/*_BTE_COMPUTER_856.xml");
        foreach($files as $file) {
            // echo $file, EOL;

            $xml = simplexml_load_file($file);

            $this->importFile($xml);

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
