<?php

namespace Supplier\XmlApi\PriceAvailability\Techdata;

use Supplier\XmlApi\Client as XmlApiClient;

class Client extends XmlApiClient
{
    const PROD_URL = '';
    const TEST_URL = '';

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
        $url = self::PROD_URL;
        $url = self::TEST_URL;

        $xml = $request->toXml();

        $response = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PriceAvailability\Techdata\Response
         */
        return new Response($response);
    }
}
