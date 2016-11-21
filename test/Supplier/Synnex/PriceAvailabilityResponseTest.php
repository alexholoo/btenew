<?php

namespace Test\Supplier\Synnex;

use PHPUnit\Framework\TestCase;
use Supplier\Synnex\PriceAvailabilityResponse;

class PriceAvailabilityResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new PriceAvailabilityResponse($xmlstr);
    }
}
