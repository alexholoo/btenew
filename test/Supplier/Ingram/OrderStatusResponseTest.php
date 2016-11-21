<?php

namespace Test\Supplier\Ingram;

use PHPUnit\Framework\TestCase;
use Supplier\Ingram\OrderStatusResponse;

class OrderStatusResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderStatusResponse($xmlstr);
    }
}
