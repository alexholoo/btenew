<?php

namespace Test\Supplier\DH;

use PHPUnit\Framework\TestCase;
use Supplier\DH\OrderStatusRequest;

class OrderStatusRequestTest extends TestCase
{
    public function testOrderStatusRequest()
    {
        $config = $this->getConfig();

        $orderId = '111-2222-33333';

        $request = new OrderStatusRequest();
        $request->setConfig($config);
        $request->setOrder($orderId);

        $xml = simplexml_load_string($request->toXml());

        $this->assertEquals($xml->getName(),        'XMLFORMPOST');
        $this->assertEquals($xml->REQUEST,          'orderStatus');
        $this->assertEquals($xml->LOGIN->USERID,    $config['username']);
        $this->assertEquals($xml->LOGIN->PASSWORD,  $config['password']);

        $this->assertEquals($xml->STATUSREQUEST->PONUM, $orderId);
    }

    protected function getConfig()
    {
        return array(
            'username'    => 'USERNAME',
            'password'    => 'PASSWORD',
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
