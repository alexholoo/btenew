<?php

namespace Test\Supplier\Techdata;

use PHPUnit\Framework\TestCase;
use Supplier\Techdata\PriceAvailabilityResponse;

class PriceAvailabilityResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PriceAvailabilityResponse($xmlstr);
    }
}
