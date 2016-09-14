<?php

namespace Supplier\Ingram;

use Utility\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderTrackingRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $loginId  = $this->config['loginId'];
        $password = $this->config['password'];
        $orderId  = $this->orderId;

        $lines = array();
        $lines[] = '<OrderTrackingRequest>';
        $lines[] = '<Version>2.0</Version>';
        $lines[] = '<TransactionHeader>';
        $lines[] =    '<SenderID></SenderID>';
        $lines[] =    '<ReceiverID></ReceiverID>';
        $lines[] =    '<CountryCode>FT</CountryCode>';
        $lines[] =    "<LoginID>$loginId</LoginID>";
        $lines[] =    "<Password>$password</Password>";
        $lines[] =    '<TransactionID></TransactionID>';
        $lines[] = '</TransactionHeader>';
        $lines[] = '<TrackingRequestHeader>';
        #lines[] =    '<BranchOrderNumber></BranchOrderNumber>';
        #lines[] =    '<OrderSuffix></OrderSuffix>';
        $lines[] =    "<CustomerPO>$orderId</CustomerPO>";
        $lines[] = '</TrackingrequestHeader>';
        $lines[] = '<ShowDetail>2</ShowDetail>';
        $lines[] = '</OrderTrackingRequest>';

        return Utils::formatXml(implode("\n", $lines));
    }
}
