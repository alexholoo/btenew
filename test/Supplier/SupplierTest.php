<?php

use PHPUnit\Framework\TestCase;

use Supplier\Supplier;
use Supplier\Prefix;
use Supplier\ConfigKey;

class SupplierTest extends TestCase
{
    /**
     * @dataProvider createClientProvider
     */
    public function testCreateClient($prefix, $class)
    {
        $client = Supplier::createClient($prefix);
        $this->assertInstanceOf($class, $client);
    }

    public function createClientProvider()
    {
        return [
            [ 'AS',   '\\Supplier\\ASI\\Client' ],
            [ 'DH',   '\\Supplier\\DH\\Client' ],
            [ 'SYN',  '\\Supplier\\Synnex\\Client' ],
            [ 'ING',  '\\Supplier\\Ingram\\Client' ],
            [ 'TD',   '\\Supplier\\Techdata\\Client' ],
        ];
    }

    public function testCreateClientReturnNull()
    {
        $client = Supplier::createClient('xx');
        $this->assertNull($client);
    }

    /**
     * @dataProvider supplierPrefixProvider
     */
    public function testSupplierPrefix($prefix, $value)
    {
        $this->assertEquals($prefix, $value);
    }

    /**
     * @dataProvider supplierPrefixProvider
     */
    public function testPrefixFromSku($prefix, $value)
    {
        $supplier = Prefix::fromSku($prefix);
        $this->assertEquals($supplier, $value);
    }

    public function supplierPrefixProvider()
    {
        return [
            [ Prefix::DH,       'DH'  ],
            [ Prefix::ASI,      'AS'  ],
            [ Prefix::INGRAM,   'ING' ],
            [ Prefix::SYNNEX,   'SYN' ],
            [ Prefix::TECHDATA, 'TD'  ],
        ];
    }

    public function testSupplierConfigKey()
    {
        $this->assertEquals(ConfigKey::DH,       'dh');
        $this->assertEquals(ConfigKey::ASI,      'asi');
        $this->assertEquals(ConfigKey::INGRAM,   'ingram');
        $this->assertEquals(ConfigKey::SYNNEX,   'synnex');
        $this->assertEquals(ConfigKey::TECHDATA, 'techdata');
    }
}
