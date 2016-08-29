<?php

namespace Supplier\XmlApi\PurchaseOrder\Techdata;

use Supplier\XmlApi\PurchaseOrder\Client as PurchaseOrderClient;

class Client extends PurchaseOrderClient
{
    const PROD_URL = 'https://tdxml.techdata.com/xmlservlet';
    const TEST_URL = 'https://tdxml.techdata.com/xmlservlet';

    /**
     * @param array|null $order
     */
    public function createRequest($order = null)
    {
        /**
         * @var Supplier\XmlApi\PurchaseOrder\Techdata\Request
         */
        $request = new Request($this->config);

        if (!empty($order)) {
            $request->addOrder($order);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PurchaseOrder\Techdata\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\PurchaseOrder\Techdata\Response
         */
        $response = new Response($res);

        $this->saveLog($url, $request, $response);

        return $response;
    }

    public function getEndpoint()
    {
        return self::TEST_URL;
        return self::PROD_URL;
    }
}
