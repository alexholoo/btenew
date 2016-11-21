<?php

namespace Test\Supplier\DH;

use PHPUnit\Framework\TestCase;
use Supplier\DH\PriceAvailabilityResponse;

class PriceAvailabilityResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PriceAvailabilityResponse($xmlstr);
    }
}
