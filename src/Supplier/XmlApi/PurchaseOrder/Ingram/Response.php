<?php

namespace Supplier\XmlApi\PurchaseOrder\Ingram;

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

    public function getXmlDoc()
    {
        return $this->xmldoc;
    }

    /**
     * @return array
     */
    public function parseXml()
    {
        $xml = simplexml_load_string($this->xmldoc);

        $this->orders['OrderNo'] = strval($xml->OrderInfo->OrderNumbers->BranchOrderNumber);

        $this->status = strval($xml->TransactionHeader->ErrorStatus['ErrorNumber']);
        $this->errorMessage = strval($xml->TransactionHeader->ErrorStatus);

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
