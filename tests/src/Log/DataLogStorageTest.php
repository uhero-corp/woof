<?php

namespace Woof\Log;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\FileDataStorage;

/**
 * @coversDefaultClass Woof\Log\DataLogStorage
 */
class DataLogStorageTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpdir;

    /**
     * @var string
     */
    private $defaultTimezone;

    /**
     * @var string
     */
    const DATA_DIR = TEST_DATA_DIR . "/Log/DataLogStorage";

    public function setUp(): void
    {
        $tmpdir = self::DATA_DIR . "/tmp";
        TestHelper::cleanDirectory($tmpdir);

        $this->tmpdir          = $tmpdir;
        $this->defaultTimezone = ini_set("timezone", "Asia/Tokyo");
    }

    public function tearDown(): void
    {
        ini_set("timezone", $this->defaultTimezone);
    }

    /**
     * prefix が空文字列の場合に InvalidArgumentException をスローします。
     *
     * @covers ::__construct
     */
    public function testConstructFailByInvalidPrefix(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DataLogStorage(new FileDataStorage($this->tmpdir), "");
    }

    /**
     * @covers ::__construct
     * @covers ::write
     * @covers ::<private>
     */
    public function testWrite(): void
    {
        $obj = new DataLogStorage(new FileDataStorage($this->tmpdir));
        foreach (["this", "is", "test"] as $content) {
            $obj->write($content, 1555500000, Logger::LEVEL_DEBUG);
        }
        foreach (["Hello", "World"] as $content) {
            $obj->write($content, 1555555555, Logger::LEVEL_DEBUG);
        }

        $expected1 = implode(PHP_EOL, ["this", "is", "test"]) . PHP_EOL;
        $expected2 = implode(PHP_EOL, ["Hello", "World"]) . PHP_EOL;
        $logPath1  = "{$this->tmpdir}/app-20190417.log";
        $logPath2  = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath1);
        $this->assertSame($expected1, file_get_contents($logPath1));
        $this->assertFileExists($logPath2);
        $this->assertSame($expected2, file_get_contents($logPath2));
    }

    /**
     * 第 2 引数以降を指定することでログファイル名をカスタマイズします。
     *
     * @covers ::__construct
     * @covers ::write
     * @covers ::<private>
     */
    public function testWriteBySuffix(): void
    {
        $obj = new DataLogStorage(new FileDataStorage($this->tmpdir), "logs/debug", ".dat");
        $obj->write("This is test", 1555500000, Logger::LEVEL_ERROR);
        $log = "{$this->tmpdir}/logs/debug-20190417.dat";
        $this->assertFileExists($log);
        $this->assertSame("This is test" . PHP_EOL, file_get_contents($log));
    }
}
