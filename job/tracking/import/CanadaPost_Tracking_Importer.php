<?php

class CanadaPost_Tracking_Importer extends Tracking_Importer
{
    public function run($argv = [])
    {
        $this->import();
    }

    public function import()
    {
        $filename = Filenames::get('canada.post.tracking');
        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $xml = simplexml_load_file($filename);

        foreach ($xml->{'delivery-request'} as $request) {

            $recipient = $request->{'delivery-spec'}->destination->recipient;
            $reference = $request->{'delivery-spec'}->reference;

            $orderId = $reference->{'customer-ref1'};
            if (!$orderId) {
                $orderId = $recipient->{'customer-client-id'};
            }

            if ($orderId) {

                $shipDate = $request->{'settlement-details'}->{'mailing-date'};
                $trackingNumber = $reference->{'item-id'};

                $this->saveToDb([
                    'orderId'        => strval($orderId),
                    'shipDate'       => strval($shipDate),
                    'carrierCode'    => 'Canada Post',
                    'carrierName'    => '',
                    'shipMethod'     => '',
                    'trackingNumber' => strval($trackingNumber),
                    'sender'         => 'BTE',
                ]);
            }
        }
    }
}
