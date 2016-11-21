<?php

namespace Test\Supplier\Techdata;

use PHPUnit\Framework\TestCase;
use Supplier\Techdata\OrderStatusResponse;

class OrderStatusResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderStatusResponse($xmlstr);
    }
}
