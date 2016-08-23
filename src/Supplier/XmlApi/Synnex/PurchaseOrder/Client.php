<?php

namespace Supplier\XmlApi\Synnex\PurchaseOrder;

use Supplier\XmlApi\Client as XmlApiClient;

class Client extends XmlApiClient
{
    const PROD_URL = 'https://ec.synnex.ca/SynnexXML/PO';
    const TEST_URL = 'https://testec.synnex.ca/SynnexXML/PO';

    /**
     * @param array|null $order
     */
    public function createRequest($order = null)
    {
        /**
         * @var Supplier\XmlApi\Synnex\PurchaseOrder\Request
         */
        $request = new Request($this->config);

        if (!empty($order)) {
            $request->addOrder($order);
        }

        return $request;
    }

    /**
     * @param Supplier\XmlApi\Synnex\PurchaseOrder\Request $request
     */
    public function sendRequest($request)
    {
        $url = self::PROD_URL;
        $url = self::TEST_URL;

        $xml = $request->toXml();

        $response = $this->curlPost($url, $xml);

        /**
         * @var Supplier\XmlApi\Synnex\PurchaseOrder\Response
         */
        return new Response($response);
    }
}
