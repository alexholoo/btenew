<?php

namespace Test\Supplier\DH;

use PHPUnit\Framework\TestCase;
use Supplier\DH\PurchaseOrderResponse;

class PurchaseOrderResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PurchaseOrderResponse($xmlstr);
    }
}
