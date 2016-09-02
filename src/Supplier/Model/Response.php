<?php

use Supplier\Model;

abstract class Response
{
    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $errorMessage;

    /**
     * @param  string $xmldoc
     */
    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc;
        $this->parseXml();
    }

    /**
     * @return string
     */
    public function getXmlDoc()
    {
        return $this->xmldoc;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @return array
     */
    abstract public function parseXml();
}
