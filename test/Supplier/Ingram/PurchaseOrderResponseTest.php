<?php

namespace Test\Supplier\Ingram;

use PHPUnit\Framework\TestCase;
use Supplier\Ingram\PurchaseOrderResponse;

class PurchaseOrderResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PurchaseOrderResponse($xmlstr);
    }
}
