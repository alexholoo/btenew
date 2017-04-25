<?php

namespace Supplier\Techdata;

use Toolkit\Utils;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\OrderStatusQueryLog;
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
            return $response->parse();
        }

        $url = self::PA_TEST_URL;
        $url = self::PA_PROD_URL;

        $request = new PriceAvailabilityRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::TECHDATA]);
        $request->addPartnum($sku);

        $xml = $request->build();

        $res = $this->curlPost($url, $xml);

        $response = new PriceAvailabilityResponse($res);
        $result = $response->parse();

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

        $xml = $request->build();
        $this->di->get('logger')->debug($xml);

        $res = $this->curlPost($url, $xml);

        $response = new PurchaseOrderResponse($res);
        $result = $response->parse();

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

        $xml = $request->build();

        $res = $this->curlPost($url, $xml);

        $response = new OrderStatusResponse($res);
        $result = $response->parse();

        $temp = $result->poNum;
        $result->poNum = $result->orderNo;
        $result->orderNo = $temp;

        OrderStatusQueryLog::save($orderId, $url, $xml, $res);

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

        $xml = $request->build();

        $res = $this->curlPost($url, $xml);

        $response = new OrderStatusResponse($res);
        $result = $response->parse();

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
