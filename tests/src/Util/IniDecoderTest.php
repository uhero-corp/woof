<?php

namespace Woof\Util;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Util\IniDecoder
 */
class IniDecoderTest extends TestCase
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
        $obj1 = IniDecoder::getInstance();
        $obj2 = IniDecoder::getInstance();
        $this->assertInstanceOf(IniDecoder::class, $obj1);
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
        $obj = IniDecoder::getInstance();
        $this->assertSame($expected, $obj->parse($src));
    }

    /**
     * @return array
     */
    public function provideTestParse(): array
    {
        $src1 = implode(PHP_EOL, ["foo = 42", "bar = 'xxxx'"]);
        $arr1 = ["foo" => 42, "bar" => "xxxx"];
        return [
            [$src1, $arr1],
            ["=", []],
        ];
    }
}
