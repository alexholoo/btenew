<?php

namespace Test\Supplier\Ingram;

use PHPUnit\Framework\TestCase;
use Supplier\Ingram\OrderDetailResponse;

class OrderDetailResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new OrderDetailResponse($xmlstr);
    }
}
