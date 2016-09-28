<?php

namespace Marketplace\eBay;

use Utility\Arr;

class Client
{
    const COMPATABILITY_LEVEL = 949;    // eBay API version

    private $requestToken;
    private $devID;
    private $appID;
    private $certID;
    private $serverUrl;
    private $compatLevel;
    private $siteID;
    private $verb;

    /**
     * __construct
     */
    public function __construct($config)
    {
        $this->requestToken = Arr::val($config, 'userToken');
        $this->devID        = Arr::val($config, 'devID');
        $this->appID        = Arr::val($config, 'appID');
        $this->certID       = Arr::val($config, 'certID');
        $this->serverUrl    = Arr::val($config, 'serverUrl');

        $this->compatLevel  = self::COMPATABILITY_LEVEL;

        $this->siteID       = Arr::val($config, 'siteID', Site::CA);
       #$this->verb         = Arr::val($config, 'callName');
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
            'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,

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

    public function getMyeBaySelling($page)
    {
        $lines = [];

        $lines[] = '<?xml version="1.0" encoding="utf-8" ?>';
        $lines[] = '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $lines[] =   "<RequesterCredentials>";
        $lines[] =     "<eBayAuthToken>{$this->requestToken}</eBayAuthToken>";
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

        // TODO: prase response to result;
        return $response;
        //return $result;
    }
}

#include '../../../public/init.php';
#
#$config = include 'config/config.php';
#$client = new Client($config['bte']);
#$res = $client->getMyeBaySelling(1);
#print_r($res);
