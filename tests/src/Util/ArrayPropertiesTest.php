<?php

namespace Woof\Util;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Util\ArrayProperties
 */
class ArrayPropertiesTest extends TestCase
{
    /**
     * @return ArrayProperties
     */
    private function createTestObject(): ArrayProperties
    {
        $json = TEST_DATA_DIR . "/Util/ArrayProperties/subjects/data.json";
        $arr  = json_decode(file_get_contents($json), true);
        return new ArrayProperties($arr);
    }

    /**
     * @covers ::__construct
     * @covers ::getData
     */
    public function testGetData(): void
    {
        $arr = [
            "key1" => [1, 2, 3],
            "key2" => "foo",
            "key3" => "bar",
        ];
        $obj = new ArrayProperties($arr);
        $this->assertSame($arr, $obj->getData());
    }

    /**
     * @param string $name
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     * @dataProvider provideTestGetFail
     */
    public function testGetFail(string $name): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = $this->createTestObject();
        $obj->get($name);
    }

    /**
     * @return array
     */
    public function provideTestGetFail(): array
    {
        return [
            [""],
            ["this/is/invalid"],
        ];
    }

    /**
     * @param string $name
     * @param mixed $expected
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     * @dataProvider provideTestGet
     */
    public function testGet(string $name, $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->get($name));
    }

    /**
     * @return array
     */
    public function provideTestGet(): array
    {
        return [
            ["key1", 123],
            ["key2", ["aaa" => "hoge", "bbb" => "fuga"]],
            ["key2.aaa", "hoge"],
            ["key3.ccc.x", true],
            ["key3.ddd.y", null],
            ["key4", null],
        ];
    }

    /**
     * @param string $name
     * @param mixed $expected
     * @covers ::__construct
     * @covers ::contains
     * @covers ::<private>
     * @dataProvider provideTestContains
     */
    public function testContains(string $name, bool $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->contains($name));
    }

    /**
     * @return array
     */
    public function provideTestContains(): array
    {
        return [
            ["key1", true],
            ["key9", false],
            ["key2.aaa", true],
            ["key2.ccc", false],
            ["key3.ccc.x", true],
            ["key3.ddd.x", false],
        ];
    }
}
