<?php

namespace Test\Supplier\Ingram;

use PHPUnit\Framework\TestCase;
use Supplier\Ingram\OrderTrackingResponse;

class OrderTrackingResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderTrackingResponse($xmlstr);
    }
}
