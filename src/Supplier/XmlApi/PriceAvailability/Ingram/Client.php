<?php

namespace Supplier\XmlApi\PriceAvailability\Ingram;

use Supplier\XmlApi\PriceAvailability\Client as PriceAvailabilityClient;

class Client extends PriceAvailabilityClient
{
    const PROD_URL = 'https://newport.ingrammicro.com/mustang';

    /**
     * @param string|null $partnum
     */
    public function createRequest($partnum = null)
    {
        /**
         * @var Supplier\XmlApi\PriceAvailability\Ingram\Request
         */
        $request = new Request($this->config);

        if (!empty($partnum)) {
            $request->addPartnum($partnum);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PriceAvailability\Ingram\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PriceAvailability\Ingram\Response
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
