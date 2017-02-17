<?php

namespace Marketplace\eBay;

use Toolkit\Arr;
use Toolkit\Utils;

class Client
{
    const COMPATABILITY_LEVEL = 949;    // eBay API version

    private $userToken;
    private $devID;
    private $appID;
    private $certID;
    private $serverUrl;
    private $apiVersion;
    private $siteID;
    private $verb;

    /**
     * __construct
     */
    public function __construct($store)
    {
        $config = require APP_DIR . '/config/ebay.php';
        $config = $config[strtolower($store)];

        $this->userToken = Arr::val($config, 'userToken');
        $this->devID     = Arr::val($config, 'devID');
        $this->appID     = Arr::val($config, 'appID');
        $this->certID    = Arr::val($config, 'certID');
        $this->serverUrl = Arr::val($config, 'serverUrl');

        $this->apiVersion = self::COMPATABILITY_LEVEL;

        $this->siteID    = Arr::val($config, 'siteID', Site::CA);
       #$this->verb      = Arr::val($config, 'callName');
    }

    /**
     * setVerb
     */
    public function setVerb($callName)
    {
        $this->verb = $callName;
    }

    /**
     * setSite
     */
    public function setSite($siteID)
    {
        $this->siteID = $siteID;
    }

    /**
     * sendHttpRequest
     *
     * Sends a HTTP request to the server for this session
     *
     * Input:   $requestBody
     * Output:  The HTTP Response as a String
     */
    public function sendHttpRequest($requestBody)
    {
        //build eBay headers using variables passed via constructor
        $headers = $this->buildEbayHeaders();

        //initialise a CURL session
        $connection = curl_init();

        // set the server we are using (could be Sandbox or Production server)
        curl_setopt($connection, CURLOPT_URL, $this->serverUrl);

        // stop CURL from verifying the peer's certificate
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);

        // set the headers using the array of headers
        curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);

        // set method as POST
        curl_setopt($connection, CURLOPT_POST, 1);

        // set the XML body of the request
        curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);

        // set it to return the transfer as a string from curl_exec
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        // Send the Request
        $response = curl_exec($connection);

        // close the connection
        curl_close($connection);

        // return the response
        return $response;
    }

    /**
     * buildEbayHeaders
     *
     * Generates an array of string to be used as the headers for the HTTP request to eBay
     * Output:  String Array of Headers applicable for this call
     */
    private function buildEbayHeaders()
    {
        $headers = array (
            //Regulates versioning of the XML interface for the API
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->apiVersion,

            //set the keys
            'X-EBAY-API-DEV-NAME: ' . $this->devID,
            'X-EBAY-API-APP-NAME: ' . $this->appID,
            'X-EBAY-API-CERT-NAME: ' . $this->certID,

            //the name of the call we are requesting
            'X-EBAY-API-CALL-NAME: ' . $this->verb,

            // SiteID must also be set in the Request's XML
            // SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
            // SiteID Indicates the eBay site to associate the call with
            'X-EBAY-API-SITEID: ' . $this->siteID,
        );

        return $headers;
    }

    public function geteBayOfficialTime()
    {
        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8"?>';
        $lines[] = '<GeteBayOfficialTimeRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   "<RequesterCredentials>";
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   "</RequesterCredentials>";
        $lines[] =   '<ErrorLanguage>en_US</ErrorLanguage>';
        #lines[] =   '<MessageID></MessageID>';
        $lines[] =   "<Version>{$this->apiVersion}</Version>";
        $lines[] =   '<WarningLevel>High</WarningLevel>';
        $lines[] = '</GeteBayOfficialTimeRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('GeteBayOfficialTime'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function getMyeBaySelling($page)
    {
        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8" ?>';
        $lines[] = '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   "<RequesterCredentials>";
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   "</RequesterCredentials>";
        $lines[] =   "<ActiveList>";
        $lines[] =     "<Include>true</Include>";
        $lines[] =     "<DetailLevel>ReturnAll</DetailLevel>";
        $lines[] =     "<Pagination>";
        $lines[] =       "<EntriesPerPage>200</EntriesPerPage>";
        $lines[] =       "<PageNumber>$page</PageNumber>";
        $lines[] =     "</Pagination>";
        $lines[] =   "</ActiveList>";
        $lines[] = '</GetMyeBaySellingRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('GetMyeBaySelling'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function completeSale($order)
    {
        $orderID = $order['OrderID'];
        $TransactionID = $order['TransactionID'];
        $trackingNumber = $order['TrackingNumber'];
        $carrier = $order['Carrier'];
        $date = $order['Date'];

        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8"?>';
        $lines[] = '<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   '<RequesterCredentials>';
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   '</RequesterCredentials>';
        $lines[] =   "<OrderID>$orderID</OrderID>";
        $lines[] =   '<Shipment>';
        $lines[] =     '<ShipmentTrackingDetails>';
        $lines[] =       '<ShipmentLineItem>';
        $lines[] =         "<TransactionID>$transactionID</TransactionID>";
        $lines[] =       '</ShipmentLineItem>';
        $lines[] =       "<ShipmentTrackingNumber>$trackingNumber</ShipmentTrackingNumber>";
        $lines[] =       "<ShippingCarrierUsed>$carrier</ShippingCarrierUsed>";
        $lines[] =     '</ShipmentTrackingDetails>';
       #$lines[] =     "<ShippedTime>$date</ShippedTime>";
        $lines[] =   '</Shipment>';
        $lines[] =   "<TransactionID>$transactionID</TransactionID>";
        $lines[] =   '<Shipped>true</Shipped>';
        $lines[] = '</CompleteSaleRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('CompleteSale'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function getOrders($timeFrom = 'Yesterday', $timeTo = 'Now')
    {
        $timeFrom = $this->toUTC(strtotime($timeFrom));
        $timeTo   = $this->toUTC(strtotime($timeTo));

        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8" ?>';
        $lines[] = '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   '<RequesterCredentials>';
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   '</RequesterCredentials>';
        $lines[] =   "<CreateTimeFrom>$timeFrom</CreateTimeFrom>";
        $lines[] =   "<CreateTimeTo>$timeTo</CreateTimeTo>";
        $lines[] =   '<OrderRole>Seller</OrderRole>';
        $lines[] =   '<OrderStatus>All</OrderStatus>';
        $lines[] =   '<DetailLevel>ReturnSummary</DetailLevel>';
        $lines[] = '</GetOrdersRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('GetOrders'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function endItem($itemID)
    {
        $lines = [];

		$lines[] = '<?xml version="1.0" encoding="utf-8"?>';
		$lines[] = '<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   '<RequesterCredentials>';
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   '</RequesterCredentials>';
		$lines[] =   "<ItemID>$itemID</ItemID>";
		$lines[] =   "<EndingReason>NotAvailable</EndingReason>";
		$lines[] = '</EndItemRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('EndItem'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function reviseInventoryStatus($info)
    {
        $itemID = $info['ItemID'];
        $quantity = $info['Quantity'];
        $price = $info['Price'];

        $lines = [];

		$lines[] = '<?xml version="1.0" encoding="utf-8"?>';
		$lines[] = '<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   '<RequesterCredentials>';
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   '</RequesterCredentials>';
		$lines[] =   '<InventoryStatus>';
		$lines[] =     "<ItemID>$itemID</ItemID>";
		$lines[] =     "<Quantity>$quantity</Quantity>";
	   #$lines[] =     "<SKU>$SKU</SKU>";
		$lines[] =     "<StartPrice>$price</StartPrice>";
		$lines[] =   '</InventoryStatus>';
		$lines[] = '</ReviseInventoryStatusRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('ReviseInventoryStatus'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    public function geteBayDetails()
    {
        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8"?>';
        $lines[] = '<GeteBayDetailsRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   '<RequesterCredentials>';
        $lines[] =     "<eBayAuthToken>{$this->userToken}</eBayAuthToken>";
        $lines[] =   '</RequesterCredentials>';
        $lines[] =   '<DetailName>ShippingCarrierDetails</DetailName>';
        $lines[] =   '<DetailName>ShippingServiceDetails</DetailName>';
        $lines[] = '</GeteBayDetailsRequest>';

        $request = implode("\n", $lines);

        $this->setVerb('GeteBayDetails'); //ucfirst(__FUNCTION__)
        $response = $this->sendHttpRequest($request);

        $this->log($request, $response);

        // TODO: prase response to result, return $result;
        return simplexml_load_string($response);
    }

    private function toUTC($time)
    {
        $tz = date_default_timezone_get();

        date_default_timezone_set('UTC');
        $timeUTC = date('Y-m-d\TH:i:s.000\Z', $time);
        date_default_timezone_set($tz);

        return $timeUTC;
    }

    private function log($request, $response)
    {
        $db = \Phalcon\Di::getDefault()->get('db');

        $db->insertAsDict('xmlapi_ebay_log', [
            'verb' => $this->verb,
            'request' => $request,
            'response' => $response,
        ]);
    }
}
