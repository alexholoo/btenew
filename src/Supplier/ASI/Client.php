<?php

namespace Supplier\ASI;

use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;

class Client extends BaseClient
{
    const PA_PROD_URL = 'https://www.asipartner.com/partneraccess/xml/price.asp';
    const PO_PROD_URL = 'https://www.asipartner.com/partneraccess/xml/order.asp';
    const OS_PROD_URL = 'https://www.asipartner.com/partneraccess/xml/shipping.asp';
    const IN_PROD_URL = 'https://www.asipartner.com/partneraccess/xml/invoice.asp';

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

        $url = self::PA_PROD_URL;

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::ASI]);
        $request->addPartnum($sku);

        $params = $request->toXml();

        $res = $this->httpGet($url . $params);

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
        $url = self::PO_PROD_URL;

        $order = $this->getPrices($order);

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::ASI]);
        $request->setOrder($order);

        $xml = $request->toXml();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new PurchaseOrderResponse($res);
        $result = $response->parseXml();

        $this->di->get('logger')->debug(Utils::formatXml($response->getXmlDoc()));

        PurchaseOrderLog::saveXml($url, $request, $response);

        if ($result->status == Response::STATUS_OK) {
            PurchaseOrderLog::save($order, $result->orderNo);
            PriceAvailabilityLog::invalidate($order);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    protected function getPrices($order)
    {
        foreach ($order->items as $key => $item) {
            $result = $this->getPriceAvailability($item->sku);
            $first = $result->getFirst();
            $order->items[$key]->price = $first->price;
        }

        return $order;
    }

    public function getOrderStatus($orderId)
    {
        $url = self::OS_PROD_URL;

        $request = new OrderStatusRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::ASI]);
        $request->setOrder($orderId);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new OrderStatusResponse($res);

        $result = $response->parseXml();

        OrderStatusQueryLog::save($orderId, $url, $xml, $res);

        if ($result->trackingNumber) {
            PurchaseOrderLog::markShipped($orderId);
            DropshipTrackingLog::save($result);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    protected function httpGet($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
