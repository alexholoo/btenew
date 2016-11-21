<?php

namespace Test\Supplier\Synnex;

use PHPUnit\Framework\TestCase;
use Supplier\Synnex\FreightQuoteResponse;

class FreightQuoteResponseTest extends TestCase
{
    public function testResponse()
    {
        $xmlstr = '';
        $response = new FreightQuoteResponse($xmlstr);
    }
}
