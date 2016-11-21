<?php

namespace Test\Supplier\DH;

use PHPUnit\Framework\TestCase;
use Supplier\DH\OrderStatusResponse;

class OrderStatusResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderStatusResponse($xmlstr);
    }
}
