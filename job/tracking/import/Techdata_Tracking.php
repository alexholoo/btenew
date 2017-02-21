<?php

class Techdata_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('techdata.tracking');

        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $columns = fgetcsv($fp);
        /*
            PO #,
            Order Number,
            Invoice Number,
            Name,
            Address 1,
            Address 2,
            City,
            Province,
            Postal Code,
            Date,
            Ship Method,
            Tracking,
            Weight(lb),
            Container Value,
            Handling Charge,
            COD Charge,
            Debit Charge,
            Freight Charge,
            Tax,
            Total
        */

        while ($fields = fgetcsv($fp)) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            list($m, $d, $y) = explode('/', $data['Date']);
            $data['Date'] = "20$y-$m-$d";

            $this->saveToDb([
                'orderId'        => $data['PO #'], // TODO: ??
                'shipDate'       => $data['Date'],
                'carrier'        => $data['Ship Method'],
                'shipMethod'     => $data['Ship Method'],
                'trackingNumber' => $data['Tracking'],
                'sender'         => 'TD-DS',
            ]);
        }

        fclose($fp);
    }
}
