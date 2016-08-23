<?php

namespace Supplier\XmlApi\DH\PriceAvailability;

use Supplier\XmlApi\Client as XmlApiClient;

class Client extends XmlApiClient
{
    const PROD_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';
    const TEST_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\DH\PriceAvailability\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\DH\PriceAvailability\Request $request
     */
    public function sendRequest($request)
    {
        $url = self::PROD_URL;
        $url = self::TEST_URL;

        $xml = $request->toXml();

        $response = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\DH\PriceAvailability\Response
         */
        return new Response($response);
    }
}
