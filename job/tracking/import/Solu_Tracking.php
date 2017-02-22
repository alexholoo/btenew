<?php

class Solu_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('solu.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

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

        while (($fields = fgetcsv($fp)) !== FALSE) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            $orderId = str_replace(' ', '', $data['Reference 1']);

            $trackingNumber = str_replace(' ', '', $data['Tracking']);
            $shipDate       = date('Y-m-d', strtotime(str_replace('/', '-', $data['Ship Date'])));
            $carrierCode    = $data['Carrier']; // i.e. DHL
            $shipMethod     = $data['Service']; // i.e. Express
           #$fullAddress    = $data['To Address'];

            $this->saveToDb([
                'orderId'        => $orderId,
                'shipDate'       => $shipDate,
                'carrier'        => $carrierCode,
                'shipMethod'     => $shipMethod,
                'trackingNumber' => $trackingNumber,
                'sender'         => 'Solu',
            ]);
        }

        fclose ($fp);
    }
}
