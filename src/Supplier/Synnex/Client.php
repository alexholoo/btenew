<?php

namespace Supplier\Synnex;

use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\ConfigKey;

class Client extends BaseClient
{
    const PA_TEST_URL = 'https://testec.synnex.ca/SynnexXML/PriceAvailability';
    const PA_PROD_URL = 'https://ec.synnex.ca/SynnexXML/PriceAvailability';

    const PO_TEST_URL = 'https://testec.synnex.ca/SynnexXML/PO';
    const PO_PROD_URL = 'https://ec.synnex.ca/SynnexXML/PO';

    /**
     * @param  string $sku
     */
    public function getPriceAvailability($sku)
    {
        if ($res = PriceAvailabilityLog::query($sku)) {
            return new PriceAvailabilityResponse($res);
        }

        $url = self::PA_PROD_URL;

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::SYNNEX]);
        $request->addPartnum($sku);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new PriceAvailabilityResponse($res);

        PriceAvailabilityLog::save($url, $request, $response);

        return $response;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function purchaseOrder($order)
    {
        $url = self::PO_TEST_URL;

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::SYNNEX]);
        $request->addOrder($order);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);

        PurchaseOrderLog::save($url, $request, $response);

        return $response;
    }
}
