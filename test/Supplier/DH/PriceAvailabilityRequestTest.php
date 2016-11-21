<?php

namespace Test\Supplier\DH;

use PHPUnit\Framework\TestCase;

use Supplier\DH\PriceAvailabilityRequest;
use Supplier\DH\PriceAvailabilityResponse;

use Supplier\DH\PurchaseOrderRequest;
use Supplier\DH\PurchaseOrderResponse;

use Supplier\DH\OrderStatusRequest;
use Supplier\DH\OrderStatusResponse;

class PriceAvailabilityRequestTest extends TestCase
{
    public function testPriceAvailabilityRequest()
    {
        $config = $this->getConfig();

        $sku = 'DH-SKU-12345';

        $request = new PriceAvailabilityRequest();
        $request->setConfig($config);
        $request->addPartnum($sku);

        $this->assertEquals($request->getPartnum(), $sku);

        $xml = simplexml_load_string($request->toXml(), 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->assertEquals($xml->REQUEST,          'price-availability');
        $this->assertEquals($xml->LOGIN->USERID,    $config['username']);
        $this->assertEquals($xml->LOGIN->PASSWORD,  $config['password']);
        $this->assertEquals($xml->PARTNUM,          substr($sku, 3));
    }

    protected function getConfig()
    {
        return array(
            'username'    => 'DHUSERNAME',
            'password'    => 'DHPASSWORD',
            'dropshippw'  => '',
            'partship'    => 'N',
            'backorder'   => 'N',
            'shipcarrier' => 'Purolator',
            'shipservice' => 'Ground',
            'onlybranch'  => 'Toronto',
            'branches'    => '', // 3, 1-7
        );
    }
}
