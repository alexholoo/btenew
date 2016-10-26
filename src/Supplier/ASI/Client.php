<?php

namespace Supplier\ASI;

use Supplier\Client as BaseClient;
use Supplier\PriceAvailabilityLog;
use Supplier\PurchaseOrderLog;
use Supplier\ConfigKey;

class Client extends BaseClient
{
    const PA_PROD_URL = 'https://www.asipartner.com/partneraccess/xml/price.asp';

    const PO_TEST_URL = '';
    const PO_PROD_URL = '';

    const OS_PROD_URL = '';

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
        throw \Exception('Purchase Order not supported for ASI');
    }

    public function getOrderStatus($orderId)
    {
        throw \Exception('Order Status not supported for ASI');
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
