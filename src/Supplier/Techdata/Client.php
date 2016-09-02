<?php

namespace Supplier\Techdata;

use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;

class Client extends BaseClient
{
    const PA_TEST_URL = '';
    const PA_PROD_URL = '';

    const PO_TEST_URL = '';
    const PO_PROD_URL = '';

    /**
     * @param  string $sku
     */
    public function getPriceAvailability($sku)
    {
        $url = 'https://tdxml.techdata.com/xmlservlet';

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->addPartnum($sku);

        if ($res = PriceAvailabilityLog::query($sku)) {
            return new PriceAvailabilityResponse($res);
        }

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new PriceAvailabilityResponse($res);

        PriceAvailabilityLog::save($url, $request, $response);

        return $response;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function purchaseOrder($order);
    {
        $url = 'https://tdxml.techdata.com/xmlservlet';

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->addOrder($order);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);

        PurchaseOrderLog::save($url, $request, $response);

        return $response;
    }
}
