<?php

namespace Test\Supplier\Synnex;

use PHPUnit\Framework\TestCase;
use Supplier\Synnex\PurchaseOrderResponse;

class PurchaseOrderResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PurchaseOrderResponse($xmlstr);
    }
}
