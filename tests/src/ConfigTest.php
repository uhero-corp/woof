<?php

namespace Woof;

use PHPUnit\Framework\TestCase;
use Woof\Util\ArrayProperties;
use Woof\Util\FileProperties;

/**
 * @coversDefaultClass Woof\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @return Config
     */
    public function createTestObject(): Config
    {
        $datadir = TEST_DATA_DIR . "/Config/subjects";
        return new Config(new FileProperties($datadir));
    }

    /**
     * @param string $name
     * @param int $expected
     * @covers ::__construct
     * @covers ::getInt
     * @dataProvider provideTestGetInt
     */
    public function testGetInt(string $name, int $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getInt("test01.{$name}", 42));
    }

    /**
     * @return array
     */
    public function provideTestGetInt(): array
    {
        return [
            ["num.a", 4],
            ["num.b", -3],
            ["num.c", 10],
            ["num.d", 2],
            ["num.e", 42],
            ["num.f", 42],
            ["num.g", 1],
            ["num.h", 0],
            ["num.i", 0],
        ];
    }

    /**
     * @param string $name
     * @param float $expected
     * @covers ::__construct
     * @covers ::getFloat
     * @dataProvider provideTestGetFloat
     */
    public function testGetFloat(string $name, float $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getFloat("test01.{$name}", 34.5));
    }

    /**
     * @return array
     */
    public function provideTestGetFloat(): array
    {
        return [
            ["num.a", 4.75],
            ["num.b", -3.125],
            ["num.c", 10.0],
            ["num.d", 2.25],
            ["num.e", 34.5],
            ["num.f", 34.5],
            ["num.g", 1.0],
            ["num.h", 0.0],
            ["num.i", 0.0],
        ];
    }

    /**
     * @param string $name
     * @param int $expected
     * @covers ::__construct
     * @covers ::getInt
     * @covers ::<private>
     * @dataProvider provideTestGetIntByMinMax
     */
    public function testGetIntByMinMax(string $name, int $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getInt("test01.{$name}", 10, -32, 16));
    }

    /**
     * @return array
     */
    public function provideTestGetIntByMinMax(): array
    {
        return [
            ["minmax.a", -32],
            ["minmax.b", -32],
            ["minmax.c", -31],
            ["minmax.d", 15],
            ["minmax.e", 16],
            ["minmax.f", 16],
            ["minmax.z", 10],
        ];
    }

    /**
     * @param string $name
     * @param float $expected
     * @covers ::__construct
     * @covers ::getFloat
     * @covers ::<private>
     * @dataProvider provideTestGetFloatByMinMax
     */
    public function testGetFloatByMinMax(string $name, float $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getFloat("test01.{$name}", 10.0, -32.0, 16.0));
    }

    /**
     * @return array
     */
    public function provideTestGetFloatByMinMax(): array
    {
        return [
            ["minmax.g", -32.0],
            ["minmax.h", -32.0],
            ["minmax.i", -31.75],
            ["minmax.j", 15.875],
            ["minmax.k", 16.0],
            ["minmax.l", 16.0],
            ["minmax.z", 10.0],
        ];
    }

    /**
     * @param string $name
     * @param string $expected
     * @covers ::__construct
     * @covers ::getString
     * @covers ::<private>
     * @dataProvider provideTestGetString
     */
    public function testGetString(string $name, string $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getString("test01.{$name}", "default"));
    }

    /**
     * @return array
     */
    public function provideTestGetString(): array
    {
        return [
            ["key1.foo","123"],
            ["key1.bar", "asdf"],
            ["key1.buzz", "true"],
            ["key1.notfound", "default"],
            ["key1", "default"],
            ["key2", "default"],
            ["key3", "default"],
            ["key4", "false"],
            ["key5", "null"],
        ];
    }

    /**
     * @param string $name
     * @param array $expected
     * @covers ::__construct
     * @covers ::getArray
     * @dataProvider provideTestGetArray
     */
    public function testGetArray(string $name, array $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getArray("test01.{$name}", ["default"]));
    }

    /**
     * @param string $name
     * @param array $expected
     * @covers ::__construct
     * @covers ::getSubConfig
     * @dataProvider provideTestGetArray
     */
    public function testGetSubConfig($name, array $expected): void
    {
        $obj = $this->createTestObject();
        $c   = new Config(new ArrayProperties($expected));
        $this->assertEquals($c, $obj->getSubConfig("test01.{$name}", ["default"]));
    }

    /**
     * @return array
     */
    public function provideTestGetArray(): array
    {
        return [
            ["key1", ["foo" => 123, "bar" => "asdf", "buzz" => true]],
            ["key1.foo", ["default"]],
            ["key2", []],
            ["key3", []],
            ["key4", ["default"]],
            ["key5", ["default"]],
            ["key9", ["default"]],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getInt
     * @covers ::getString
     * @covers ::getArray
     * @covers ::getBool
     */
    public function testGetByDefault(): void
    {
        $c   = new Config(new ArrayProperties([]));
        $obj = $this->createTestObject();
        $this->assertSame(0, $obj->getInt("notfound.key1"));
        $this->assertSame(0.0, $obj->getFloat("notfound.key1"));
        $this->assertSame("", $obj->getString("notfound.key1"));
        $this->assertSame([], $obj->getArray("notfound.key1"));
        $this->assertEquals($c, $obj->getSubConfig("notfound.key1"));
        $this->assertSame(false, $obj->getBool("notfound.key1"));
    }

    /**
     * @param string $name
     * @param bool $expected
     * @covers ::__construct
     * @covers ::getBool
     * @covers ::<private>
     * @dataProvider provideTestGetBool
     */
    public function testGetBool(string $name, bool $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->getBool("test01.{$name}", false));
    }

    /**
     * @return array
     */
    public function provideTestGetBool(): array
    {
        return [
            ["key1.foo", false],
            ["key1.bar", false],
            ["key1.buzz", true],
            ["key6.a", true],
            ["key6.b", false],
            ["key6.c", true],
            ["key6.d", false],
            ["key6.e", true],
            ["key6.f", false]
        ];
    }

    /**
     * @param string $name
     * @param bool $expected
     * @covers ::__construct
     * @covers ::contains
     * @dataProvider provideTestContains
     */
    public function testContains(string $name, bool $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->contains("test01.{$name}"));
    }

    /**
     * @return array
     */
    public function provideTestContains(): array
    {
        return [
            ["key1.foo", true],
            ["key1.notfound", false],
            ["key2", true],
            ["key3", true],
            ["key4", true],
            ["key5", true],
            ["key9", false],
        ];
    }
}
