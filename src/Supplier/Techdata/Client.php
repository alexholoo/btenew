<?php

namespace Supplier\Techdata;

use Toolkit\Utils;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\OrderStatusQueryLog;
use Supplier\DropshipTrackingLog;
use Supplier\ConfigKey;
use Supplier\Model\Response;

class Client extends BaseClient
{
    const PA_TEST_URL = 'http://tdxml.cstenet.com/xmlservlet';
    const PA_PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

    const PO_TEST_URL = 'http://tdxml.cstenet.com/xmlservlet';
    const PO_PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

    const OS_PROD_URL = 'https://tdxml.techdata.com/xmlservlet';

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
        $request->setOrder($order);

        $xml = $request->toXml();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);
        $result = $response->parseXml();

        $this->di->get('logger')->debug(Utils::formatXml($response->getXmlDoc()));

        PurchaseOrderLog::saveXml($url, $request, $response);

        if ($result->status == Response::STATUS_OK && isset($order['sku'])) {
            PurchaseOrderLog::save($order['sku'], $order['orderId'], $result->orderNo, 'dropship');
            PriceAvailabilityLog::invalidate($order['sku']);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    public function getOrderStatus($orderId)
    {
        $url = self::OS_PROD_URL;

        $detail = $this->getOrderDetail($orderId);

        $request = new OrderStatusRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->setPurpose('03'); // Shipment Detail
        $request->setOrderNum($detail->orderNo);
        $request->setPoNumber($detail->poNum);
        $request->setInvoice($detail->invoice);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new OrderStatusResponse($res);
        $result = $response->parseXml();

        $temp = $result->poNum;
        $result->poNum = $result->orderNo;
        $result->orderNo = $temp;

        OrderStatusQueryLog::save($orderId, $url, $xml, $res);

        if ($result->trackingNumber) {
            PurchaseOrderLog::markShipped($orderId);
            DropshipTrackingLog::save($result);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    public function getOrderDetail($orderId)
    {
        $url = self::OS_PROD_URL;

        $request = new OrderStatusRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->setPurpose('01'); // Order Detail
        $request->setPoNumber($orderId);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml);

        $response = new OrderStatusResponse($res);
        $result = $response->parseXml();

        OrderStatusQueryLog::save($orderId, $url, $xml, $res);

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    public function getInvoiceDetail($orderId, $invoice)
    {
        $request->setPurpose('02'); // Invoice Detail
    }
}
