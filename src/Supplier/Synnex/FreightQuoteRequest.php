<?php

namespace Supplier\Synnex;

use Utility\Utils;
use Supplier\Model\Request as BaseRequest;

class FreightQuoteRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $lines = array();
        $lines[] = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines[] = '<SynnexB2B>';
        $lines[] = $this->credential();
        $lines[] = $this->freightQuoteRequest();
        $lines[] = '</SynnexB2B>';

        return Utils::formatXml(implode("\n", $lines));
    }

    protected function credential()
    {
        $username = $this->config['username'];
        $password = $this->config['password'];

        $lines = array();
        $lines[] = '<Credential>';
        $lines[] =   "<UserID>$username</UserID>";
        $lines[] =   "<Password>$password</Password>";
        $lines[] = '</Credential>';

        return implode("\n", $lines);
    }

    protected function freightQuoteRequest()
    {
        $lines = array();

        return implode("\n", $lines);
    }
}
