<?php

namespace Woof\Http;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\QualityValues
 */
class QualityValuesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::<private>
     * @expectedException InvalidArgumentException
     * @dataProvider provideTestConstructFailByInvalidQvalueList
     */
    public function testConstructFailByInvalidQvalueList(array $qvalueList): void
    {
        $this->expectException(InvalidArgumentException::class);
        new QualityValues("Accept-Language", $qvalueList);
    }

    /**
     * @return array
     */
    public function provideTestConstructFailByInvalidQvalueList(): array
    {
        return [
            [[]],
            [["a,b,c" => 1.0]],
            [["ja" => 1.5, "en" => 0.5]],
            [["ja" => 1.0, "en" => -1]],
            [["ja" => "asdf"]],
        ];
    }

    /**
     * @param array $qvalueList
     * @param string $expected
     * @covers ::__construct
     * @covers ::format
     * @dataProvider provideTestFormat
     */
    public function testFormat(array $qvalueList, string $expected): void
    {
        $obj = new QualityValues("Accept-Language", $qvalueList);
        $this->assertSame($expected, $obj->format());
    }

    /**
     * @return array
     */
    public function provideTestFormat(): array
    {
        return [
            [["en" => 0.2, "ja" => 1.0, "en-GB" => 0.5, "en-US" => 0.7], "ja,en-US;q=0.7,en-GB;q=0.5,en;q=0.2"],
            [["ja" => "1.0", "en" => "0.75"], "ja,en;q=0.75"],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $obj = new QualityValues("Accept-Language", ["ja" => 1.0]);
        $this->assertSame("Accept-Language", $obj->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::<private>
     * @covers ::getValue
     * @dataProvider provideTestGetValue
     */
    public function testGetValue(array $qvalueList, array $expected): void
    {
        $obj = new QualityValues("Accept-Language", $qvalueList);
        $this->assertSame($expected, $obj->getValue());
    }

    /**
     * @return array
     */
    public function provideTestGetValue(): array
    {
        return [
            [
                ["en" => 0.7, "ja" => 1.0, "en-GB" => 0.8, "en-US" => 0.9],
                ["ja" => "1", "en-US" => "0.9", "en-GB" => "0.8", "en" => "0.7"],
            ],
            [
                ["ja" => 6 / 7, "en" => 3 / 4, "de" => 1 / 5, "fr" => 1 / 8],
                ["ja" => "0.857", "en" => "0.75", "de" => "0.2", "fr" => "0.125"],
            ],
            [
                ["ja" => "1.000", "en" => ".9", "de" => "0"],
                ["ja" => "1.000", "en" => ".9", "de" => "0"],
            ]
        ];
    }
}
