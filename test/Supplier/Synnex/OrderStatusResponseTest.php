<?php

namespace Test\Supplier\Synnex;

use PHPUnit\Framework\TestCase;
use Supplier\Synnex\OrderStatusResponse;

class OrderStatusResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderStatusResponse($xmlstr);
    }
}
