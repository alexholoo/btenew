<?php

namespace Supplier\Synnex;

use Utility\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderStatusRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $username   = $this->config['username'];
        $password   = $this->config['password'];
        $customerNo = $this->config['customerNo'];
        $orderId    = $this->orderId;

        $lines = array();
        $lines = '<?xml version="1.0" encoding="UTF-8" ?>';
        $lines = '<SynnexB2B version="2.2">';
        $lines =     '<Credential>';
        $lines =        "<UserID>$username</UserID>";
        $lines =        "<Password>$password</Password>";
        $lines =     '</Credential>';
        $lines =     '<OrderStatusRequest>';
        $lines =        "<CustomerNumber>$customerNo</CustomerNumber>";
        $lines =        "<PONumber>$orderId</PONumber>";
        $lines =     '</OrderStatusRequest>';
        $lines = '</SynnexB2B>';

        return Utils::formatXml(implode("\n", $lines));
    }
}
