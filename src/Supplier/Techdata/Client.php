<?php

namespace Supplier\Techdata;

use Utility\Utils;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;
use Supplier\Model\Response;

class Client extends BaseClient
{
    const PA_TEST_URL = 'http://tdxml.cstenet.com/xmlservlet';
    const PA_PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

    const PO_TEST_URL = 'http://tdxml.cstenet.com/xmlservlet';
    const PO_PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

    /**
     * @param  string $sku
     */
    public function getPriceAvailability($sku)
    {
        if ($res = PriceAvailabilityLog::query($sku)) {
            $response = new PriceAvailabilityResponse($res);
            $this->request = null;
            $this->response = $response;
            return $response->parseXml();
        }

        $url = self::PA_TEST_URL;
        $url = self::PA_PROD_URL;

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->addPartnum($sku);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new PriceAvailabilityResponse($res);
        $result = $response->parseXml();

        PriceAvailabilityLog::save($url, $request, $response);

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function purchaseOrder($order)
    {
        $url = self::PO_TEST_URL;
        $url = self::PO_PROD_URL;

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->addOrder($order);

        $xml = $request->toXml();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);
        $result = $response->parseXml();

        $this->di->get('logger')->debug(Utils::formatXml($response->getXmlDoc()));

        PurchaseOrderLog::saveXml($url, $request, $response);

        if ($result->status == Response::STATUS_OK) {
            PurchaseOrderLog::save($order['sku'], $order['orderId'], $result->orderNo);
            PriceAvailabilityLog::invalidate($order['sku']);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }
}
