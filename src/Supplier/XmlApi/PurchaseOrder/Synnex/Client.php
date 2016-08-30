<?php

namespace Supplier\XmlApi\PurchaseOrder\Synnex;

use Supplier\XmlApi\PurchaseOrder\Client as PurchaseOrderClient;

class Client extends PurchaseOrderClient
{
    const PROD_URL = 'https://ec.synnex.ca/SynnexXML/PO';
    const TEST_URL = 'https://testec.synnex.ca/SynnexXML/PO';

    /**
     * @param array|null $order
     */
    public function createRequest($order = null)
    {
        /**
         * @var Supplier\XmlApi\PurchaseOrder\Synnex\Request
         */
        $request = new Request($this->config);

        if (!empty($order)) {
            $request->addOrder($order);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\PurchaseOrder\Synnex\Request $request
     */
    public function sendRequest($request)
    {
        $url = $this->getEndpoint();

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        /**
         * @var Supplier\XmlApi\PurchaseOrder\Synnex\Response
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
