<?php

namespace Marketplace\Bestbuy;

class Client
{
    protected $apikey;
    protected $hostname = "https://marketplace.bestbuy.ca/";

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->config = $this->di->get('config');
        $this->apikey = $this->config->bestbuy->apikey;
    }

    public function listCarriers()
    {
        $json = $this->callApi('GET', 'api/shipping/carriers');
        return $json;
    }

    public function listOrders($start = '', $end = '')
    {
        if (!$start) {
            $start = date('Y-m-d\T00:00:00');
            $end   = date('Y-m-d\T23:59:59');
        }

        $params = [
            'start_date' => $start,
            'end_date'   => $end,
            'max'        => 100,
        ];

        $json = $this->callApi('GET', 'api/orders', $params);

        $orders = [];

        foreach ($json->orders as $order) {
            $address = $order->customer->shipping_address;
            /**
             *  "order_state":
             *      "STAGING" |
             *      "WAITING_ACCEPTANCE" |
             *      "WAITING_DEBIT" |
             *      "WAITING_DEBIT_PAYMENT" |
             *      "SHIPPING" |
             *      "SHIPPED" |
             *      "TO_COLLECT" |
             *      "RECEIVED" |
             *      "CLOSED" |
             *      "REFUSED" |
             *      "CANCELED",
             */
            // address is empty for CANCELED orders
            if ($order->order_state == 'CANCELED' || empty($address)) {
                $address = new \stdClass();
                $address->firstname = $order->customer->firstname;
                $address->lastname  = $order->customer->lastname;
                $address->street_1  = '';
                $address->street_2  = '';
                $address->city      = '';
                $address->state     = '';
                $address->country   = '';
                $address->zip_code  = '';
                $address->phone     = '';
            }

            foreach ($order->order_lines as $item) {
                $orders[] = [
                    'date'        => substr($order->created_date, 0, 10),
                    'orderId'     => $order->order_id,
                    'orderItemId' => $item->order_line_id,
                    'sku'         => $item->offer_sku,
                    'price'       => $item->price,
                    'qty'         => $item->quantity,
                    'shipping'    => $item->shipping_price,
                    'product'     => $item->product_title,
                    'express'     => intval($order->shipping_type_code == 'E'),
                    'status'      => $order->order_state,
                    'buyer'       => $address->firstname.' '.$address->lastname,
                    'address'     => $address->street_1.' '.$address->street_2,
                    'city'        => $address->city,
                    'state'       => $address->state,
                    'country'     => $address->country,
                    'zipcode'     => $address->zip_code,
                    'phone'       => $address->phone,
                ];
            }
        }

        return $orders;
    }

    public function listOffers()
    {
        return $this->callApi('GET', 'api/offers');
    }

    public function updateTracking($orderId, $tracking)
    {
        /**
         * carrierCode:
         *   - CPCL
         *   - ASYN
         *   - PRLA
         *   - UPSN
         *   - LTL
         *   - Other:carrierName
         */
        $carrier = strtoupper($tracking['carrierCode']);
        if ($carrier == 'OTHER') {
            $carrier = strtoupper($tracking['carrierName']);
        }

        if ($carrier == 'CANADA POST') {
            $carrier = 'CPCL';
        }
        if ($carrier == 'PUROLATOR') {
            $carrier = 'PRLA';
        }
        if ($carrier == 'UPS') {
            $carrier = 'UPSN';
        }

        $trackingInfo = [
            'carrier_code'    => $carrier,
           #'carrier_name'    => $tracking['carrierName'],
           #'carrier_url'     => $tracking['carrierUrl'],
            'tracking_number' => $tracking['trackingNumber'],
        ];

        return $this->callApi('PUT', "api/orders/$orderId/tracking", $trackingInfo);
    }

    public function markOrderAsShipped($orderId)
    {
        return $this->callApi('PUT', "api/orders/$orderId/ship");
    }

    public function callApi($method, $apiUrl, $params = [])
    {
        $method = strtoupper($method);

        $url = $this->hostname . trim($apiUrl, '/');

        $options['http']['method'] = $method;
        $options['http']['header'] = "Authorization: " . $this->apikey . "\r\n";

        if ($method == 'GET' && $params) {
            $url .= '?' . http_build_query($params);
        }

        if (($method == 'PUT' || $method == 'POST') && $params) {
            $options['http']['content'] = json_encode($params);
            $options['http']['header'] .= "Content-Type: application/json\r\n";
        }

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $json = json_decode($result);
        // print_r($json);

        return $json;
    }
}
