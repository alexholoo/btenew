<?php

namespace Supplier\ASI;

use Toolkit\Utils;
use Supplier\Model\PurchaseOrderRequest as BaseRequest;

class PurchaseOrderRequest extends BaseRequest
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
