<?php

namespace Supplier\Ingram;

use Toolkit\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderTrackingRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function build()
    {
        $loginId  = $this->config['loginId'];
        $password = $this->config['password'];
        $orderId  = $this->orderId;

        $lines = array();

        $lines[] = '<OrderTrackingRequest>';
        $lines[] =   '<Version>2.0</Version>';
        $lines[] =   '<TransactionHeader>';
        $lines[] =      '<SenderID>ME</SenderID>';
        $lines[] =      '<ReceiverID>YOU</ReceiverID>';
        $lines[] =      '<CountryCode>FT</CountryCode>';
        $lines[] =      "<LoginID>$loginId</LoginID>";
        $lines[] =      "<Password>$password</Password>";
        $lines[] =      '<TransactionID>1</TransactionID>';
        $lines[] =   '</TransactionHeader>';
        $lines[] =   '<TrackingRequestHeader>';
        #lines[] =      '<BranchOrderNumber></BranchOrderNumber>';
        #lines[] =      '<OrderSuffix></OrderSuffix>';
        $lines[] =      "<CustomerPO>$orderId</CustomerPO>";
        $lines[] =   '</TrackingRequestHeader>';
        $lines[] =   '<ShowDetail>2</ShowDetail>';
        $lines[] = '</OrderTrackingRequest>';

        return Utils::formatXml(implode("\n", $lines));
    }
}
