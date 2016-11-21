<?php

use PHPUnit\Framework\TestCase;

use Toolkit\AmericaState;

class AmericaStateTest extends TestCase
{
    /**
     * @dataProvider nameToCodeProvider
     */
    public function testNameToCode($name, $expected)
    {
        $this->assertTrue(AmericaState::nameToCode($name) == $expected);
    }

    public function nameToCodeProvider()
    {
        return [
            [ ' new   york  ',          'NY' ],
            [ 'xx',                     'xx' ],
        ];
    }

    /**
     * @dataProvider codeToNameProvider
     */
    public function testCodeToName($price, $expected)
    {
        $this->assertTrue(AmericaState::codeToName($price) == $expected);
    }

    public function codeToNameProvider()
    {
        return [
            [ 'ny', 'New York' ],
            [ 'NY', 'New York' ],
            [ 'xx', 'xx'       ],
        ];
    }
}
