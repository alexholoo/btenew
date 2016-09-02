<?php

namespace Supplier;

use Phalcon\Di;

abstract class Client
{
    /**
     * constructor
     */
    public function __construct()
    {
         $this->di = Di::getDefault();
         $this->config = $this->di->get('config');
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

        foreach ($options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param  string $sku
     */
    abstract public function getPriceAvailability($sku);

    /**
     * @param  Supplier\Model\Order $order
     */
    abstract public function purchaseOrder($order);
}
