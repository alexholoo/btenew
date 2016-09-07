<?php

namespace Supplier\Model;

abstract class Request
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    abstract public function toXml();
}
