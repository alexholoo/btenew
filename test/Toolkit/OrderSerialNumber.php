<?php

namespace Test\Toolkit;

use PHPUnit\Framework\TestCase;
use Toolkit\OrderSerialNumber;

class OrderSerialNumberTest extends TestCase
{
    public function testOrderSeqNum()
    {
        $rand = rand();

        $orderSeqNum = $this->getMock('OrderSerialNumber', array('getLastInsertId'));
        $orderSeqNum
            ->expects($this->once())
            ->method('getLastInsertId')
            ->will($this->returnValue($rand));

        $seq = orderSeqNum->get('ABC');
        $this->assertRegExp('/ABC-\d{6}-\d{5}/', $seq);
    }
}
