<?php

namespace Supplier\XmlApi\PurchaseOrder\Ingram;

class Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $order;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function addOrder($order)
    {
        $this->order = $order;
    }

    public function toXml()
    {
        $lines = array();

        return implode("\n", $lines);
    }

    public function login()
    {
        $lines = array();

        $userid = $this->config['username'];
        $passwd = $this->config['password'];


        return implode("\n", $lines);
    }

    public function orderHeader()
    {
        $lines = array();


        return implode("\n", $lines);
    }

    public function orderItems()
    {
        $lines = array();

        if (substr($sku, 0, 3) == 'ING-') {
            $sku = substr($sku, 3);
        }

        return implode("\n", $lines);
    }
}
