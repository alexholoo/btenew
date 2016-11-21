<?php

namespace Test\Supplier\ASI;

use PHPUnit\Framework\TestCase;
use Supplier\ASI\PriceAvailabilityResponse;

class PriceAvailabilityResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PriceAvailabilityResponse($xmlstr);
    }
}
