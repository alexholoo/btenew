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
        $lines = array();
        // ...
        return Utils::formatXml(implode("\n", $lines));
    }
}
