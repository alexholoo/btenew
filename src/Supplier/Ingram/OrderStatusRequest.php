<?php

namespace Supplier\Ingram;

use Utility\Utils;
use Supplier\Model\OrderStatusRequest as BaseRequest;

class OrderStatusRequest extends BaseRequest
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
