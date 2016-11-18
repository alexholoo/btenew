<?php

use PHPUnit\Framework\TestCase;

use Toolkit\Utils;

class ToolkitTest extends TestCase
{
    protected function setUp()
    {
    }

    /**
     * @dataProvider safePriceProvider
     */
    public function testSafePrice($price, $expected)
    {
        $this->assertTrue(Utils::safePrice($price) == $expected);
    }

    public function safePriceProvider()
    {
        return [
            [ '$123',    '123'  ],
            [ 'CA$123',  '123'  ],
            [ '$12.3',   '12.3' ],
            [ 'CA$12.3', '12.3' ],
        ];
    }

    /**
     * @dataProvider phoneNumberProvider
     */
    public function testFormatPhoneNumber($phoneNumber, $sep, $expected)
    {
        $this->assertTrue(Utils::formatPhoneNumber($phoneNumber, $sep) == $expected);
    }

    public function phoneNumberProvider()
    {
        return [
            [ '1234567890',   '-', '123-456-7890' ],
            [ '+11234567890', '-', '123-456-7890' ],
            [ '1234567890',   '.', '123.456.7890' ],
        ];
    }

    /**
     * @dataProvider zipcodeProvider
     */
    public function testFormatCanadaZipCode($zipcode, $expected)
    {
        $this->assertTrue(Utils::formatCanadaZipCode($zipcode) == $expected);
    }

    public function zipcodeProvider()
    {
        return [
            [ 'A1B2C3',     'A1B 2C3' ],
            [ 'a1b2c3',     'A1B 2C3' ],
            [ 'a1b  2c3',   'A1B 2C3' ],
            [ 'a  1b  2c3', 'A1B 2C3' ],
        ];
    }
}
