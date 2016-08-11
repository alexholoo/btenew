<?php

namespace Supplier\XmlApi\Synnex\PriceAvailability;

use Supplier\XmlApi\Client as XmlApiClient;

class Client extends XmlApiClient 
{
    const PROD_URL = 'https://ec.synnex.ca/SynnexXML/PriceAvailability';
    const TEST_URL = 'https://testec.synnex.ca/SynnexXML/PriceAvailability';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\Synnex\PriceAvailability\Request
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
         * @var Supplier\XmlApi\Synnex\PriceAvailability\Response
         */
        return new Response($response);
    }
}
