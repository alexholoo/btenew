<?php

namespace Supplier\Synnex;

use Utility\Utils;
use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;
use Supplier\Model\Response;

class Client extends BaseClient
{
    const PA_TEST_URL = 'https://testec.synnex.ca/SynnexXML/PriceAvailability';
    const PA_PROD_URL = 'https://ec.synnex.ca/SynnexXML/PriceAvailability';

    const PO_TEST_URL = 'https://testec.synnex.ca/SynnexXML/PO';
    const PO_PROD_URL = 'https://ec.synnex.ca/SynnexXML/PO';

    const FQ_PROD_URL = 'https://ec.synnex.ca/SynnexXML/FreightQuote';
    const FQ_TEST_URL = 'https://testec.synnex.ca/SynnexXML/FreightQuote';

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
        $request->setConfig($this->config['xmlapi'][ConfigKey::SYNNEX]);
        $request->addPartnum($sku);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

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

        $result = $this->getPriceAvailability($order['sku']);
        $item = $result->getFirst();
        if ($item->price) {
            $order['price'] = $item->price;
        } else {
            $order['price'] = 1.0; // $order['price'] * 0.5;
        }

        $request = new PurchaseOrderRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::SYNNEX]);
        $request->addOrder($order);

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
            PurchaseOrderLog::save($order['sku'], $order['orderId'], $result->orderNo);
            PriceAvailabilityLog::invalidate($order['sku']);
        }

        $this->request = $request;
        $this->response = $response;

        return $result;
    }

    /**
     * @param  Supplier\Model\Order $order
     */
    public function getFreightQuote($order)
    {
        $url = self::FQ_PROD_URL;
        $url = self::FQ_TEST_URL;

        $request = new FreightQuoteRequest();
        $request->setConfig($this->config['xmlapi'][ConfigKey::SYNNEX]);
        $request->addOrder($order);

        $xml = $request->toXml();

        $res = $this->curlPost($url, $xml, array(
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
        ));

        $response = new FreightQuoteResponse($res);
        $result = $response->parseXml();

        $this->request = $request;
        $this->response = $response;

        return $result;
    }
}
