<?php

namespace Supplier\XmlApi\PriceAvailability\Techdata;

use Supplier\XmlApi\PriceAvailability\Client as PriceAvailabilityClient;

class Client extends PriceAvailabilityClient
{
    const PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\PriceAvailability\Techdata\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PriceAvailability\Techdata\Request $request
     */
    public function sendRequest($request)
    {
        if ($res = $this->queryLog($request->getPartnum())) {
            return new Response($res);
        }

        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PriceAvailability\Techdata\Response
         */
        $response = new Response($res);

        $this->saveLog($url, $request, $response);

        return $response;
    }

    public function getEndpoint()
    {
        return self::PROD_URL;
    }
}
