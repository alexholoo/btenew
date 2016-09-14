<?php

namespace Supplier\Model;

abstract class Request
{
    /**
     * @var \Supplier\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param \Supplier\Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return boolean
     */
    abstract protected function initConfig();

    /**
     * @return string
     */
    abstract public function toXml();
}
