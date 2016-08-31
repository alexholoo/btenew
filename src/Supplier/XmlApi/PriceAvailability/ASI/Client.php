<?php

namespace Supplier\XmlApi\PriceAvailability\ASI;

use Supplier\XmlApi\PriceAvailability\Client as PriceAvailabilityClient;

class Client extends PriceAvailabilityClient
{
    const PROD_URL = 'https://www.asipartner.com/partneraccess/xml/price.asp';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\PriceAvailability\ASI\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PriceAvailability\ASI\Request $request
     */
    public function sendRequest($request)
    {
        if ($res = $this->queryLog($request->getPartnum())) {
            return new Response($res);
        }

        $url  = $this->getEndpoint();

        $cid  = $this->config['CID'];
        $cert = $this->config['CERT'];
        $sku  = $request->getPartnum();

        if (substr($sku, 0, 3) == 'AS-') {
            $sku = substr($sku, 3);
        }

        $url .= "?CID=$cid&CERT=$cert&SKU=$sku";

        $res = $this->httpGet($url);

        /**
         * @var Supplier\XmlApi\PriceAvailability\ASI\Response
         */
        $response = new Response($res);

        $this->saveLog($url, $request, $response);

        return $response;
    }

    public function getEndpoint()
    {
        return self::PROD_URL;
    }

    protected function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
