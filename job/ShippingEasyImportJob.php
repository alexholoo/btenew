<?php

include 'classes/Job.php';

class ShippingEasyImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->importShippingEasy();
    }

    protected function importShippingEasy()
    {
        $filename = 'w:/out/shipping/shippingeasy-shipping-report.csv';

        $this->log("Importing $filename");

        $fp = fopen($filename, 'r');

        // skip the first few lines
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);
        fgetcsv($fp); fgetcsv($fp); fgetcsv($fp);

        while ($fields = fgetcsv($fp)) {
            try {
                $success = $this->db->insertAsDict('shippingeasy', [
                    'ShipDate'                 => $fields[0],
                    'User'                     => $fields[1],
                    'OrderDate'                => $fields[2],
                    'OrderTotal'               => $fields[3],
                    'Store'                    => $fields[4],
                    'OrderNumber'              => trim($fields[5]),
                    'ShipFrom'                 => $fields[6],
                    'ShipFromAddress'          => $fields[7],
                    'Recipient'                => $fields[8],
                    'RecipientBillingAddress'  => $fields[9],
                    'RecipientShippingAddress' => $fields[10],
                    'EmailAddress'             => $fields[11],
                    'Carrier'                  => $fields[12],
                    'RateProvider'             => $fields[13],
                    'ServiceType'              => $fields[14],
                    'PackageType'              => $fields[15],
                    'ConfirmationOption'       => $fields[16],
                    'Quantity'                 => $fields[17],
                    'WeightOZ'                 => $fields[18],
                    'Zone'                     => $fields[19],
                    'DestinationCountry'       => $fields[20],
                    'DestinationCity'          => $fields[21],
                    'DestinationStateProvince' => $fields[22],
                    'TrackingNumber'           => ltrim($fields[23], "'"),
                    'ShippingPaidByCustomer'   => $fields[24],
                    'PostageCost'              => $fields[25],
                    'InsuranceCost'            => $fields[26],
                    'TotalShippingCost'        => $fields[27],
                    'ShippingMargin'           => $fields[28],
                    'SKU'                      => $fields[29],
                    'ItemName'                 => $fields[30],
                ]);
            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }

        fclose($fp);
    }

    // This code is not tested.
    protected function downloadShippingEasy()
    {
        include "../vendor/ShippingEasy/shipping_easy-php/lib/ShippingEasy.php";

        $config = $this->di->get('config');

        ShippingEasy::setApiKey($config->shippingEasy->apiKey);
        ShippingEasy::setApiSecret($config->shippingEasy->apiSecret);

        $orderApi = new ShippingEasy_Order();

        $pageNum = 1;

        do {
            $resp = $orderApi->findAll([
                //"status" => array("ready_for_shipment", "shipped"),
                "page" => $pageNum,
                "last_updated_at" => date('Y-m-d 00:00:00', strtotime('-1 days'))
            ]);

            foreach ($resp['orders'] as $order) {
                $data = $this->parseOrder($order);
                // do something with $data
            }

            $pageNum += 1;

            $meta = $resp['meta'];
            $totalPages = $meta['total_pages'];
        } while ($pageNum <= $totalPages);
    }

    // This code is not tested, some field values may not correct.
    protected function parseOrder($order)
    {
        $recipient = $order['recipients'][0];
        $item = $recipient['line_items'][0];
        $shipments = $order['shipments'][0];

        $billingAddress = function($order) {
            if (trim($order['billing_address2'])) {
                $order['billing_address'] .= ' '.$order['billing_address2'];
            }
            return implode(', ', [
               #$order['billing_company'],
               #$order['billing_first_name'],
               #$order['billing_last_name'],
                $order['billing_address'],
                $order['billing_city'],
                $order['billing_state'],
                $order['billing_postal_code'],
                $order['billing_country'],
               #$order['billing_phone_number'],
            ]);
        };

        $shippingAddress = function($order) {
            $recipient = $order['recipients'][0];
            if (trim($recipient['address2'])) {
                $recipient['address'] .= ' '.$recipient['address2'];
            }
            if (trim($recipient['address3'])) {
                $recipient['address'] .= ' '.$recipient['address3'];
            }
            return implode(', ', [
                $recipient['address'],
                $recipient['city'],
                $recipient['state'],
               #$recipient['province'],
                $recipient['postal_code'],
                $recipient['postal_code_plus_4'],
                $recipient['country'],
               #$recipient['phone_number'],
               #$recipient['shipping_method'],
            ]);
        };

        $data = [
            'ShipDate'                 => $shipments['ship_date'],
            'User'                     => 'Roy Zhang',
            'OrderDate'                => substr($order['ordered_at'], 0, 10),
            'OrderTotal'               => $order['total_including_tax'],
            'Store'                    => 'OneDealOutlet Online',
            'OrderNumber'              => $order['external_order_identifier'],
            'ShipFrom'                 => 'BTE C/O Borderworx Logistics',
            'ShipFromAddress'          => '369 Lang Blvd., Grand Island, NY, 14072, 3123, United States',
            'Recipient'                => $recipient['first_name'].' '.$recipient['last_name'],
            'RecipientBillingAddress'  => $billingAddress($order),
            'RecipientShippingAddress' => $shippingAddress($order),
            'EmailAddress'             => $recipient['email'],
            'Carrier'                  => $shipments['carrier_key'],
            'RateProvider'             => '', //??
            'ServiceType'              => $shipments['carrier_service_key'], //??
            'PackageType'              => '', //??
            'ConfirmationOption'       => '',
            'Quantity'                 => $item['quantity'],
            'WeightOZ'                 => $item['weight_in_ounces'], //??
            'Zone'                     => $recipient['original_order']['usps_shipping_zone'],
            'DestinationCountry'       => $recipient['country'],
            'DestinationCity'          => ucfirst(strtolower($recipient['city'])),
            'DestinationStateProvince' => $recipient['state'],
            'TrackingNumber'           => $shipments['tracking_number'],
            'ShippingPaidByCustomer'   => $order['base_shipping_cost'],
            'PostageCost'              => round($shipments['shipment_cost']/100.0, 2),
            'InsuranceCost'            => '0.00', //??
            'TotalShippingCost'        => round($shipments['shipment_cost']/100.0, 2),
            'ShippingMargin'           => round($order['base_shipping_cost']-$shipments['shipment_cost']/100.0, 2),
            'SKU'                      => $item['sku'],
            'ItemName'                 => $item['item_name'],
        ];

        // not always correct
        if ($data['ServiceType'] == 'First') {
            $data['ServiceType'] = 'First Class Mail';
            $data['RateProvider'] = 'Endicia First Class Domestic';
        }

        if ($data['ServiceType'] == 'Priority') {
            $data['ServiceType'] = 'Priority Mail';
            $data['RateProvider'] = 'Endicia International & Expedited';
        }

        return $data;
    }
}

include __DIR__ . '/../public/init.php';

$job = new ShippingEasyImportJob();
$job->run($argv);
