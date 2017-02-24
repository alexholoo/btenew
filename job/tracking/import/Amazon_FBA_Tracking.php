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
        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        // TODO: need a class: Marketplace\Amazon\FbaReportFile

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

        while (($values = fgetcsv($fp, 0, "\t"))) {
            if (count($columns) != count($values)) {
                $this->error(__METHOD__. print_r($values, true));
                continue;
            }
            $fields = array_combine($columns, $values);

            $orderId = $fields['merchant-order-id'];
            if (empty($orderId)) {
                $orderId = $fields['amazon-order-id'];
            }

            $this->saveToDb([
                'orderId'        => $fields['amazon-order-id'],
                'shipDate'       => $fields['shipment-date'],
                'carrier'        => $fields['carrier'],
                'shipMethod'     => '',
                'trackingNumber' => $fields['tracking-number'],
                'sender'         => $site,
            ]);
        }

        fclose($fp);
    }
}
