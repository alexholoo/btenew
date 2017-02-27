<?php

class Solu_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('solu.tracking');

        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        // TODO: need a class Shipment\SoluTrackingFile

        $columns = fgetcsv($fp);
        /*
            Order Id,
            Tracking,
            Ship Date,
            Carrier,
            Service,
            Q/B,
            Weight,
            Quoted Charge,
            Billed Charge,
            From Address,
            To Address,
            Status,
            Billing Status,
            Reference 1,
            Reference 2
        */

        while (($values = fgetcsv($fp)) !== FALSE) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            $orderId = str_replace(' ', '', $fields['Reference 1']);

            $trackingNumber = str_replace(' ', '', $fields['Tracking']);
            $shipDate       = date('Y-m-d', strtotime(str_replace('/', '-', $fields['Ship Date'])));
            $carrierCode    = $fields['Carrier']; // i.e. DHL
            $shipMethod     = $fields['Service']; // i.e. Express
           #$fullAddress    = $fields['To Address'];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrierCode'    => $carrierCode,
                'carrierName'    => '',
                'shipMethod'     => $shipMethod,
                'trackingNumber' => $trackingNumber,
                'sender'         => 'Solu',
            ]);
        }

        fclose ($fp);
    }
}
