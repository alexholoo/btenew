<?php

class CanadaPost_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('canada.post.tracking');
        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        #if (IS_PROD) {
        #    $filename = 'E:/BTE/shipping/cpc.xml';
        #}

        $xml = simplexml_load_file($filename);

        foreach ($xml->{'delivery-request'} as $request) {

            $recipient = $request->{'delivery-spec'}->destination->recipient;
            $reference = $request->{'delivery-spec'}->reference;

            $orderId = $recipient->{'customer-client-id'};
            if (!$orderId) {
                $orderId = $reference->{'customer-ref1'};
            }

            if ($orderId) {

                $shipDate = $request->{'settlement-details'}->{'mailing-date'};
                $trackingNumber = $reference->{'item-id'};

                $this->saveToDb([
                    'orderId'        => strval($orderId),
                    'shipDate'       => strval($shipDate),
                    'carrier'        => 'Canada Post',
                    'shipMethod'     => '',
                    'trackingNumber' => strval($trackingNumber),
                    'sender'         => 'BTE',
                ]);
            }
        }
    }
}
