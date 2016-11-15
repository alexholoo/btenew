<?php

namespace Supplier\Model;

use Supplier\Model\Request;

abstract class BatchPurchaseRequest extends Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $orders;

    /**
     * @var array
     */
    protected $address;

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $order
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param array $info
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }
}
