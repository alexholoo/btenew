<?php

namespace Supplier\XmlApi\PurchaseOrder\DH;

use Supplier\XmlApi\Client as XmlApiClient;

class Client extends XmlApiClient
{
    const PROD_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';
    const TEST_URL = 'https://www.dandh.ca/dhXML/xmlDispatch';

    /**
     * @param array|null $order
     */
    public function createRequest($order = null)
    {
        /**
         * @var Supplier\XmlApi\PurchaseOrder\DH\Request
         */
        $request = new Request($this->config);

        if (!empty($order)) {
            $request->addOrder($order);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PurchaseOrder\DH\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $response = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PurchaseOrder\DH\Response
         */
        return new Response($response);
    }

    public function getEndpoint()
    {
        return self::TEST_URL;
        return self::PROD_URL;
    }
}
