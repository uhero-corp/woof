<?php

namespace Woof\Log;

use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\System\FixedClock;

/**
 * @coversDefaultClass Woof\Log\Logger
 */
class LoggerTest extends TestCase
{
    /**
     * @var string
     */
    const DATA_DIR = TEST_DATA_DIR . "/Log/Logger";

    /**
     * @var LogStorage
     */
    private $storage;

    /**
     * @var string
     */
    private $tmpdir;

    /**
     * @var string
     */
    private $defaultTimezone;

    public function setUp(): void
    {
        $datadir = self::DATA_DIR;
        $tmpdir  = "{$datadir}/tmp";
        TestHelper::cleanDirectory($tmpdir);

        $this->tmpdir          = $tmpdir;
        $this->storage         = new FileLogStorage($tmpdir);
        $this->defaultTimezone = ini_set("date.timezone", "Asia/Tokyo");
    }

    public function tearDown(): void
    {
        ini_set("date.timezone", $this->defaultTimezone);
    }

    /**
     * @covers ::getNopLogger
     */
    public function testGetNopLogger(): void
    {
        $obj1 = Logger::getNopLogger();
        $obj2 = Logger::getNopLogger();
        $this->assertSame($obj1, $obj2);
        $this->assertSame(-1, $obj1->getLogLevel());
        $this->assertTrue($obj1->error("test"));
    }

    /**
     * @covers ::newInstance
     * @covers ::__construct
     * @covers ::getLogLevel
     */
    public function testGetLogLevel(): void
    {
        $builder = new LoggerBuilder();
        $builder->setStorage($this->storage);
        $builder->setLogLevel(Logger::LEVEL_INFO);

        $obj = $builder->build();
        $this->assertSame(Logger::LEVEL_INFO, $obj->getLogLevel());
    }

    /**
     * @covers ::newInstance
     * @covers ::__construct
     * @covers ::isMultiple
     */
    public function testIsMultiple(): void
    {
        $builder = new LoggerBuilder();
        $builder->setStorage($this->storage);

        $obj1 = $builder->build();
        $this->assertFalse($obj1->isMultiple());

        $builder->setMultiple(false);
        $obj2 = $builder->build();
        $this->assertFalse($obj2->isMultiple());

        $builder->setMultiple(true);
        $obj3 = $builder->build();
        $this->assertTrue($obj3->isMultiple());
    }

    /**
     *
     * @covers ::newInstance
     * @covers ::__construct
     * @covers ::getFormat
     */
    public function testGetFormat(): void
    {
        $defaultFormat = new DefaultLogFormat();
        $customFormat  = new DefaultLogFormat("Y/m/d H:i:s");
        $builder       = new LoggerBuilder();
        $builder->setStorage($this->storage);

        $obj1 = $builder->build();
        $this->assertEquals($defaultFormat, $obj1->getFormat());

        $builder->setFormat($customFormat);
        $obj2 = $builder->build();
        $this->assertSame($customFormat, $obj2->getFormat());
    }

    /**
     * @covers ::newInstance
     * @covers ::__construct
     * @covers ::getStorage
     */
    public function testGetStorage(): void
    {
        $builder = new LoggerBuilder();
        $obj1    = $builder->build();
        $this->assertSame(NullLogStorage::getInstance(), $obj1->getStorage());
        $builder->setStorage($this->storage);
        $obj2    = $builder->build();
        $this->assertSame($this->storage, $obj2->getStorage());
    }

    /**
     * @covers ::newInstance
     * @covers ::__construct
     * @covers ::getClock
     */
    public function testGetClock(): void
    {
        $builder = new LoggerBuilder();
        $clock   = new FixedClock(1555555555);
        $builder->setStorage($this->storage);
        $builder->setClock($clock);

        $obj = $builder->build();
        $this->assertSame($clock, $obj->getClock());
    }

    /**
     * @param int $level
     * @return Logger
     */
    private function getTestObjectByLogLevel(int $level): Logger
    {
        $builder = new LoggerBuilder();
        $builder->setStorage($this->storage);
        $builder->setClock(new FixedClock(1555555555));
        $builder->setLogLevel($level);
        return $builder->build();
    }

    /**
     * @param bool $expected
     */
    private function checkLogCreated(bool $expected): void
    {
        $logCreated = file_exists("{$this->tmpdir}/app-20190418.log");
        $this->assertSame($expected, $logCreated);
    }

    /**
     * Logger に設定されたログレベルの値に関わらず、常にログの追記が行われます。
     *
     * @param int $level
     * @param bool $expected
     * @dataProvider provideTestError
     * @covers ::error
     * @covers ::<private>
     */
    public function testError(int $level, bool $expected): void
    {
        $this->getTestObjectByLogLevel($level)->error("test");
        $this->checkLogCreated($expected);
    }

    /**
     * @return array
     */
    public function provideTestError(): array
    {
        return [
            [Logger::LEVEL_ERROR, true],
            [Logger::LEVEL_ALERT, true],
            [Logger::LEVEL_INFO, true],
            [Logger::LEVEL_DEBUG, true],
        ];
    }

    /**
     * Logger に設定されたログレベルが DEBUG, INFO, ALERT の場合のみログの追記が行われます。
     *
     * @param int $level
     * @param bool $expected
     * @dataProvider provideTestAlert
     * @covers ::alert
     * @covers ::<private>
     */
    public function testAlert(int $level, bool $expected): void
    {
        $this->getTestObjectByLogLevel($level)->alert("test");
        $this->checkLogCreated($expected);
    }

    /**
     *
     * @return array
     */
    public function provideTestAlert(): array
    {
        return [
            [Logger::LEVEL_ERROR, false],
            [Logger::LEVEL_ALERT, true],
            [Logger::LEVEL_INFO, true],
            [Logger::LEVEL_DEBUG, true],
        ];
    }

    /**
     * Logger に設定されたログレベルが INFO, DEBUG の場合のみログの追記が行われます。
     *
     * @param int $level
     * @param bool $expected
     * @dataProvider provideTestInfo
     * @covers ::info
     * @covers ::<private>
     */
    public function testInfo($level, $expected): void
    {
        $this->getTestObjectByLogLevel($level)->info("test");
        $this->checkLogCreated($expected);
    }

    /**
     *
     * @return array
     */
    public function provideTestInfo(): array
    {
        return [
            [Logger::LEVEL_ERROR, false],
            [Logger::LEVEL_ALERT, false],
            [Logger::LEVEL_INFO, true],
            [Logger::LEVEL_DEBUG, true],
        ];
    }

    /**
     * Logger に設定されたログレベルが DEBUG の場合のみログの追記が行われます。
     *
     * @param int $level
     * @param bool $expected
     * @dataProvider provideTestDebug
     * @covers ::debug
     * @covers ::<private>
     */
    public function testDebug(int $level, bool $expected): void
    {
        $this->getTestObjectByLogLevel($level)->debug("test");
        $this->checkLogCreated($expected);
    }

    /**
     *
     * @return array
     */
    public function provideTestDebug(): array
    {
        return [
            [Logger::LEVEL_ERROR, false],
            [Logger::LEVEL_ALERT, false],
            [Logger::LEVEL_INFO, false],
            [Logger::LEVEL_DEBUG, true],
        ];
    }

    /**
     * オブジェクトをログに追記した場合は __toString() の結果が書き込まれます。
     *
     * @covers ::log
     * @covers ::<private>
     */
    public function testLogWithToString(): void
    {
        $sample = new LoggerTest_Sample1();
        $obj    = $this->getTestObjectByLogLevel(Logger::LEVEL_ERROR);
        $obj->error($sample);

        $logPath  = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath);
        $expected = "[2019-04-18 11:45:55][ERROR] THIS_IS_TEST" . PHP_EOL;
        $this->assertSame($expected, file_get_contents($logPath));
    }

    /**
     * __toString() を実装していないオブジェクトをログに追記した場合は print_r による文字列表現が書き込まれます。
     *
     * @covers ::log
     * @covers ::<private>
     */
    public function testLogWithPlainObject(): void
    {
        $sample = new LoggerTest_Sample2();
        $obj    = $this->getTestObjectByLogLevel(Logger::LEVEL_ERROR);
        $obj->error($sample);

        $logPath  = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath);
        $lines    = [
            "[2019-04-18 11:45:55][ERROR] Woof\\Log\\LoggerTest_Sample2 Object",
            "[2019-04-18 11:45:55][ERROR] (",
            "[2019-04-18 11:45:55][ERROR] )",
        ];
        $expected = implode(PHP_EOL, $lines) . PHP_EOL;
        $this->assertSame($expected, file_get_contents($logPath));
    }

    /**
     * 配列をログに追記した場合は print_r の結果が書き込まれます。
     *
     * @covers ::log
     * @covers ::<private>
     */
    public function testLogWithArray(): void
    {
        $obj = $this->getTestObjectByLogLevel(Logger::LEVEL_ERROR);
        $obj->error(["hoge" => 1, "fuga" => "test"]);

        $logPath  = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath);
        $lines    = [
            "[2019-04-18 11:45:55][ERROR] Array",
            "[2019-04-18 11:45:55][ERROR] (",
            "[2019-04-18 11:45:55][ERROR]     [hoge] => 1",
            "[2019-04-18 11:45:55][ERROR]     [fuga] => test",
            "[2019-04-18 11:45:55][ERROR] )",
        ];
        $expected = implode(PHP_EOL, $lines) . PHP_EOL;
        $this->assertSame($expected, file_get_contents($logPath));
    }

    /**
     * 文字列以外のスカラー値をログに追記した場合は、それぞれの型に応じた文字列表現が書き込まれます。
     *
     * @param mixed $value
     * @param string $expected
     * @dataProvider provideTestLogWithScalar
     * @covers ::log
     * @covers ::<private>
     */
    public function testLogWithScalar($value, string $expected): void
    {
        $obj = $this->getTestObjectByLogLevel(Logger::LEVEL_ERROR);
        $obj->error($value);

        $logPath   = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath);
        $expected2 = "[2019-04-18 11:45:55][ERROR] {$expected}" . PHP_EOL;
        $this->assertSame($expected2, file_get_contents($logPath));
    }

    /**
     * @return array
     */
    public function provideTestLogWithScalar(): array
    {
        return [
            [true, "(TRUE)"],
            [false, "(FALSE)"],
            [null, "(NULL)"],
            [1.25, "1.25"],
            [-123, "-123"],
        ];
    }

    /**
     *
     * @param bool $multiple
     * @param array $expected
     * @dataProvider provideTestLogByMultiple
     * @covers ::log
     * @covers ::<private>
     */
    public function testLogByMultiple(bool $multiple, array $expected): void
    {
        $lines = [
            "Hello",
            "World",
            "Test",
        ];
        $value = implode(PHP_EOL, $lines);

        $builder = new LoggerBuilder();
        $builder->setStorage($this->storage);
        $builder->setClock(new FixedClock(1555555555));
        $builder->setMultiple($multiple);
        $obj     = $builder->build();
        $obj->error($value);

        $logPath   = "{$this->tmpdir}/app-20190418.log";
        $this->assertFileExists($logPath);
        $expected2 = implode(PHP_EOL, $expected) . PHP_EOL;
        $this->assertSame($expected2, file_get_contents($logPath));
    }

    /**
     * @return array
     */
    public function provideTestLogByMultiple(): array
    {
        $expected1 = [
            "[2019-04-18 11:45:55][ERROR] Hello",
            "[2019-04-18 11:45:55][ERROR] World",
            "[2019-04-18 11:45:55][ERROR] Test",
        ];
        $expected2 = [
            "[2019-04-18 11:45:55][ERROR] Hello",
            "World",
            "Test",
        ];
        return [
            [false, $expected1],
            [true, $expected2],
        ];
    }
}

class LoggerTest_Sample1
{
    /**
     *
     * @return string
     */
    public function __toString(): string
    {
        return "THIS_IS_TEST";
    }
}

class LoggerTest_Sample2
{

}
