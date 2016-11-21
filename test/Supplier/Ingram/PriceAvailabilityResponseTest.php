<?php

namespace Test\Supplier\Ingram;

use PHPUnit\Framework\TestCase;
use Supplier\Ingram\PriceAvailabilityResponse;

class PriceAvailabilityResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PriceAvailabilityResponse($xmlstr);
    }
}
