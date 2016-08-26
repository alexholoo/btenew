<?php

namespace Supplier\XmlApi\PriceAvailability\DH;

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
         * @var Supplier\XmlApi\PriceAvailability\DH\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PriceAvailability\DH\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $response = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PriceAvailability\DH\Response
         */
        return new Response($response);
    }

    public function getEndpoint()
    {
        return self::TEST_URL;
        return self::PROD_URL;
    }
}
