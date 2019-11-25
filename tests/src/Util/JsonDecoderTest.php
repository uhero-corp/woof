<?php

namespace Woof\Util;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Util\JsonDecoder
 */
class JsonDecoderTest extends TestCase
{
    /**
     * @var int
     */
    private $errorReporting;

    public function setUp(): void
    {
        $this->errorReporting = error_reporting(0);
    }

    public function tearDown(): void
    {
        error_reporting($this->errorReporting);
    }

    /**
     * @covers ::getInstance
     */
    public function testGetInstance(): void
    {
        $obj1 = JsonDecoder::getInstance();
        $obj2 = JsonDecoder::getInstance();
        $this->assertInstanceOf(JsonDecoder::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @param string $src
     * @param array $expected
     * @covers ::getInstance
     * @covers ::parse
     * @dataProvider provideTestParse
     */
    public function testParse(string $src, array $expected): void
    {
        $obj = JsonDecoder::getInstance();
        $this->assertSame($expected, $obj->parse($src));
    }

    /**
     * @return array
     */
    public function provideTestParse(): array
    {
        return [
            ['{"foo": 1, "bar": "xxx"}', ["foo" => 1, "bar" => "xxx"]],
            ['"asdf"', []],
            ['{invalid}', []],
        ];
    }
}
