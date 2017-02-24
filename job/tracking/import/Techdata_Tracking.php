<?php

class Techdata_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('techdata.tracking');

        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        // TODO: need a class: Supplier\Synnex\TrackingFile

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

        while ($values = fgetcsv($fp)) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__ . print_r($values, true));
                continue;
            }

            $fields = array_combine($columns, $values);

            list($m, $d, $y) = explode('/', $fields['Date']);
            $fields['Date'] = "20$y-$m-$d";

            $this->saveToDb([
                'orderId'        => $fields['PO #'], // TODO: ??
                'shipDate'       => $fields['Date'],
                'carrier'        => $fields['Ship Method'],
                'shipMethod'     => $fields['Ship Method'],
                'trackingNumber' => $fields['Tracking'],
                'sender'         => 'TD-DS',
            ]);
        }

        fclose($fp);
    }
}
