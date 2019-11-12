<?php

namespace Woof\Log;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Log\DefaultLogFormat
 */
class DefaultLogFormatTest extends TestCase
{
    /**
     * このテストではタイムゾーンを Asia/Tokyo に固定します。
     */
    public function setUp(): void
    {
        $this->defaultTimezone = ini_set("date.timezone", "Asia/Tokyo");
    }

    /**
     * 固定したタイムゾーンを元の状態に戻します。
     */
    public function tearDown(): void
    {
        ini_set("date.timezone", $this->defaultTimezone);
    }

    /**
     * コンストラクタ引数を省略した場合、時刻のフォーマットが "YYYY-MM-DD HH:MM:DD" 形式となります。
     *
     * @covers ::__construct
     */
    public function testConstructDefault(): void
    {
        $obj    = new DefaultLogFormat();
        $result = $obj->format("TEST", 1234567890, Logger::LEVEL_DEBUG);
        $this->assertSame("[2009-02-14 08:31:30][DEBUG] TEST", $result);
    }

    /**
     * コンストラクタ引数を指定した場合、引数のフォーマットで時刻を書式化します。
     *
     * @covers ::__construct
     */
    public function testConstructSpecified(): void
    {
        $obj    = new DefaultLogFormat("Y.n.j H:i:s");
        $result = $obj->format("TEST", 1234567890, Logger::LEVEL_DEBUG);
        $this->assertSame("[2009.2.14 08:31:30][DEBUG] TEST", $result);
    }

    /**
     * 引数に指定されたログレベル定数を対応する文字列 ("ERROR" など) に変換して出力します。
     *
     * @param int $level
     * @param string $expected
     * @covers ::__construct
     * @covers ::format
     * @covers ::formatLogLevel
     * @dataProvider provideTestFormatByLevel
     */
    public function testFormatByLevel(int $level, string $expected): void
    {
        $obj    = new DefaultLogFormat();
        $result = $obj->format("hogehoge", 1234567890, $level);
        $this->assertSame($expected, $result);
    }

    /**
     * @return array
     */
    public function provideTestFormatByLevel(): array
    {
        return [
            [Logger::LEVEL_ERROR, "[2009-02-14 08:31:30][ERROR] hogehoge"],
            [Logger::LEVEL_ALERT, "[2009-02-14 08:31:30][ALERT] hogehoge"],
            [Logger::LEVEL_INFO,  "[2009-02-14 08:31:30][INFO ] hogehoge"],
            [Logger::LEVEL_DEBUG, "[2009-02-14 08:31:30][DEBUG] hogehoge"],
        ];
    }
}
