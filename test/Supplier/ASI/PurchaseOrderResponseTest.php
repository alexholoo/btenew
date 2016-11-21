<?php

namespace Test\Supplier\ASI;

use PHPUnit\Framework\TestCase;
use Supplier\ASI\PurchaseOrderResponse;

class PurchaseOrderResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PurchaseOrderResponse($xmlstr);
    }
}
