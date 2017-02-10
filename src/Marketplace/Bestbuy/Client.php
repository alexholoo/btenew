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

    public function listOrders($start = '', $end = '')
    {
        if (!$start) {
            $start = date('Y-m-d\T00:00:00');
            $end   = date('Y-m-d\T23:59:59');
        }

        $params = [
            'start_date' => $start,
            'end_date'   => $end,
        ];

        return $this->callApi('GET', 'api/orders', $params);
    }

    public function listOffers()
    {
        return $this->callApi('GET', 'api/offers');
    }

    public function updateTracking($orderId, $trackingInfo)
    {
        /*{
            "carrier_code": String,
            "carrier_name": String,
            "carrier_url": String,
            "tracking_number": String
        }*/

        return $this->callApi('PUT', "api/orders/$orderId/tracking", $trackingInfo);
    }

    public function markOrderAsShipped($orderId)
    {
        return $this->callApi('PUT', "api/orders/$orderId/ship");
    }

    public function callApi($method, $apiUrl, $params = [])
    {
        $url = $this->hostname . trim($apiUrl, '/');
        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $options = [
            'http' => [
                'header'  => "Authorization: " . $this->apikey . "\r\n",
                'method'  => strtoupper($method),
               #'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $json = json_decode($result);
        // print_r($json);

        return $json;
    }
}
