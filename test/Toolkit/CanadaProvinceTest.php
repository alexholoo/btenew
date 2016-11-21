<?php

use PHPUnit\Framework\TestCase;

use Toolkit\CanadaProvince;

class CanadaProvinceTest extends TestCase
{
    /**
     * @dataProvider nameToCodeProvider
     */
    public function testNameToCode($name, $expected)
    {
        $this->assertTrue(CanadaProvince::nameToCode($name) == $expected);
    }

    public function nameToCodeProvider()
    {
        return [
            [ 'Québec',                 'QC' ],
            [ 'Quebec',                 'QC' ],
            [ ' british  columbia ',    'BC' ],
            [ ' nova   scotia ',        'NS' ],
            [ 'Prince Edward Island',   'PE' ],
            [ 'Northwest Territories',  'NT' ],
            [ 'xx',                     'xx' ],
        ];
    }

    /**
     * @dataProvider codeToNameProvider
     */
    public function testCodeToName($price, $expected)
    {
        $this->assertTrue(CanadaProvince::codeToName($price) == $expected);
    }

    public function codeToNameProvider()
    {
        return [
            [ 'qc', 'Quebec' ],
            [ 'QC', 'Quebec' ],
            [ 'xx', 'xx'     ],
        ];
    }
}
