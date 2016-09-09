<?php

namespace Supplier\Ingram;

use Utility\Utils;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;

class Client extends BaseClient
{
    const PA_PROD_URL = 'https://newport.ingrammicro.com/mustang';

    const PO_TEST_URL = 'https://newport.ingrammicro.com/mustang';
    const PO_PROD_URL = 'https://newport.ingrammicro.com/mustang';

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
        $request->setConfig($this->config['xmlapi'][ConfigKey::INGRAM]);
        $request->addPartnum($sku);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

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
        $request->setConfig($this->config['xmlapi'][ConfigKey::INGRAM]);
        $request->addOrder($order);

        $xml = $request->toXml();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);
        $this->di->get('logger')->debug(Utils::formatXml($response->getXmlDoc()));

        PurchaseOrderLog::save($url, $request, $response);
        PriceAvailabilityLog::invalidate($order['sku']);

        return $response;
    }
}
