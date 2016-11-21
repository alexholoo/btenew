<?php

namespace Test\Supplier\Techdata;

use PHPUnit\Framework\TestCase;
use Supplier\Techdata\PurchaseOrderResponse;

class PurchaseOrderResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PurchaseOrderResponse($xmlstr);
    }
}
