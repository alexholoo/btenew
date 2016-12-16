<?php

class CanadaPost_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function merge()
    {
        $filename = 'w:/out/shipping/cpc.xml';

        #if (gethostname() == 'BTELENOVO') {
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

            $name    = $recipient->{'contact-name'};
            $address = $recipient->{'address-line-1'};
            $city    = $recipient->{'city'};
            $state   = $recipient->{'prov-state'};
            $zip     = $recipient->{'postal-zip-code'};
            $country = $recipient->{'country-code'};

            $fullAddress = "$name, $address, $city, $state, $zip, $country";

            if ($orderId) {

                $orderItemId = '';
                $quantity = '';
                $shipDate = $request->{'settlement-details'}->{'mailing-date'};
                $carrierCode = 'Canada Post';
                $carrierName = '';
                $trackingNumber = $reference->{'item-id'};
                $shipMethod = 'BTE';
                $site= 'Canada';

                //echo "$orderId $trackingNumber\n";

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

                if ($country == 'CA' && $this->amazonCAshipment) {
                    $this->amazonCAshipment->write($row);
                }

                if ($country == 'MX' && $this->amazonUSshipment) {
                    $this->amazonUSshipment->write($row);
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
}
