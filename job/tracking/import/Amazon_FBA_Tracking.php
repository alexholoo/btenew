<?php

class Amazon_FBA_Tracking extends TrackingImporter
{
    public function import()
    {
        $filename = Filenames::get('amazon.ca.fba.tracking');
        $this->importTracking($filename, 'Amazon_CA_FBA');

        $filename = Filenames::get('amazon.us.fba.tracking');
        $this->importTracking($filename, 'Amazon_US_FBA');
    }

    public function importTracking($filename, $site)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        $columns = fgetcsv($fp, 0, "\t");
        /*{
            amazon-order-id
            merchant-order-id
            shipment-id
            shipment-item-id
            amazon-order-item-id
            merchant-order-item-id
            purchase-date
            payments-date
            shipment-date
            reporting-date
            buyer-email
            buyer-name
            buyer-phone-number
            sku
            product-name
            quantity-shipped
            currency
            item-price
            item-tax
            shipping-price
            shipping-tax
            gift-wrap-price
            gift-wrap-tax
            ship-service-level
            recipient-name
            ship-address-1
            ship-address-2
            ship-address-3
            ship-city
            ship-state
            ship-postal-code
            ship-country
            ship-phone-number
            bill-address-1
            bill-address-2
            bill-address-3
            bill-city
            bill-state
            bill-postal-code
            bill-country
            item-promotion-discount
            ship-promotion-discount
            carrier
            tracking-number
            estimated-arrival-date
            fulfillment-center-id
            fulfillment-channel
            sales-channel
        }*/

        while (($fields = fgetcsv($fp, 0, "\t"))) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__. print_r($fields, true));
                continue;
            }
            $data = array_combine($columns, $fields);

            $this->saveToDb([
                'orderId'        => $data['amazon-order-id'],
                'shipDate'       => $data['shipment-date'],
                'carrier'        => $data['carrier'],
                'shipMethod'     => '',
                'trackingNumber' => $data['tracking-number'],
                'sender'         => $site,
            ]);

            if ($data['merchant-order-id']) {
                $this->saveToDb([
                    'orderId'        => $data['merchant-order-id'],
                    'shipDate'       => $data['shipment-date'],
                    'carrier'        => $data['carrier'],
                    'shipMethod'     => '',
                    'trackingNumber' => $data['tracking-number'],
                    'sender'         => $site,
                ]);
            }
        }

        fclose($fp);
    }
}
