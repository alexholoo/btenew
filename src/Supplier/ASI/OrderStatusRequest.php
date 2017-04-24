<?php

namespace Supplier\ASI;

use Utility\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderStatusRequest extends BaseRequest
{
    /**
     * @return string
     */
    public function toXml()
    {
        $cid = $this->config['CID'];
        $orderId = $this->orderId;

        return "?cid=$cid&po=$orderId";
    }
}

