<?php

namespace Supplier;

use Phalcon\Di;

abstract class Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var Supplier\Model\Request
     */
    protected $request;

    /**
     * @var Supplier\Model\Response
     */
    protected $response;

    /**
     * constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->di = Di::getDefault();
        $this->config = $config;

        if (!$config) {
            $this->config = $this->di->get('config');
        }
    }

    /**
     * @param  string $url
     * @param  string $data
     * @param  array  $options
     */
    public function curlPost($url, $data, $options = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       #curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain'));

        foreach ($options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getXmlApiConfig($supplier)
    {
        return $this->config['xmlapi'][$supplier];
    }

    /**
     * @return Supplier\Model\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Supplier\Model\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param  string $sku
     * @return Supplier\Model\PriceAvailabilityResult
     */
    abstract public function getPriceAvailability($sku);

    /**
     * @param  Supplier\Model\Order $order
     * @return Supplier\Model\PurchaseOrderResult
     */
    abstract public function purchaseOrder($order);

    /**
     * @param  string $orderId
     * @param  string $invoice
     * @return Supplier\Model\OrderQueryRequest
     */
    abstract public function getOrderStatus($orderId, $invoice = '');
}
