<?php

namespace Supplier\XmlApi\PurchaseOrder\Techdata;

class Response
{
    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $orders;

    /**
     * @param string $xmldoc
     */
    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc;
        $this->parseXml();
    }

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        return $this->orders;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}

