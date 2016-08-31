<?php

namespace Supplier\XmlApi\PriceAvailability\Synnex;

use Supplier\XmlApi\PriceAvailability\Client as PriceAvailabilityClient;

class Client extends PriceAvailabilityClient
{
    const PROD_URL = 'https://ec.synnex.ca/SynnexXML/PriceAvailability';
    const TEST_URL = 'https://testec.synnex.ca/SynnexXML/PriceAvailability';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\PriceAvailability\Synnex\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PriceAvailability\Synnex\Request $request
     */
    public function sendRequest($request)
    {
        if ($res = $this->queryLog($request->getPartnum())) {
            return new Response($res);
        }

        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        /**
         * @var Supplier\XmlApi\PriceAvailability\Synnex\Response
         */
        $response = new Response($res);

        $this->saveLog($url, $request, $response);

        return $response;
    }

    public function getEndpoint()
    {
        return self::PROD_URL;
        return self::TEST_URL;
    }
}
