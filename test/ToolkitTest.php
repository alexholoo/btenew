<?php

use PHPUnit\Framework\TestCase;

use Toolkit\Utils;

class ToolkitTest extends TestCase
{
    protected function setUp()
    {
    }

    public function testSamePrice()
    {
        $this->assertTrue(Utils::safePrice('$123') == '123');
        $this->assertTrue(Utils::safePrice('CA$123') == '123');

        $this->assertTrue(Utils::safePrice('$12.3') == '12.3');
        $this->assertTrue(Utils::safePrice('CA$12.3') == '12.3');
    }

    public function testFormatPhoneNumber()
    {
        $this->assertTrue(Utils::formatPhoneNumber('1234567890') == '123-456-7890');
        $this->assertTrue(Utils::formatPhoneNumber('+11234567890') == '123-456-7890');
        $this->assertTrue(Utils::formatPhoneNumber('1234567890', '.') == '123.456.7890');
    }

    public function testFormatCanadaZipCode()
    {
        $this->assertTrue(Utils::formatCanadaZipCode('A1B2C3') == 'A1B 2C3');
        $this->assertTrue(Utils::formatCanadaZipCode('a1b2c3') == 'A1B 2C3');
        $this->assertTrue(Utils::formatCanadaZipCode('a1b  2c3') == 'A1B 2C3');
        $this->assertTrue(Utils::formatCanadaZipCode('a  1b  2c3') == 'A1B 2C3');
    }
}
