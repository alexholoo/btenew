<?php

namespace Supplier\Model;

abstract class Response
{
    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';

    /**
     * @var string
     */
    protected $xmldoc;

    /**
     * @param  string $xmldoc
     */
    public function __construct($xmldoc)
    {
        $this->xmldoc = $xmldoc;
    }

    /**
     * @return string
     */
    public function getXmlDoc()
    {
        return $this->xmldoc;
    }
}
