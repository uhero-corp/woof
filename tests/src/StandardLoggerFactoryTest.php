<?php

namespace Woof;

use PHPUnit\Framework\TestCase;
use Woof\Log\DataLogStorage;
use Woof\Log\DefaultLogFormat;
use Woof\Log\Logger;
use Woof\Util\ArrayProperties;

/**
 * @coversDefaultClass Woof\StandardLoggerFactory
 */
class StandardLoggerFactoryTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpdir;

    public function setUp(): void
    {
        $basedir      = TEST_DATA_DIR . "/StandardLoggerFactory";
        $this->tmpdir = "{$basedir}/tmp";
    }

    /**
     * @param array $prop
     * @return Logger
     */
    private function createLoggerByArray(array $prop): Logger
    {
        $obj  = new StandardLoggerFactory();
        $data = new FileDataStorage($this->tmpdir);
        $conf = new Config(new ArrayProperties($prop));
        return $obj->create($conf, $data);
    }

    /**
     * @param string $level
     * @param int $expected
     * @covers ::create
     * @covers ::detectLogLevel
     * @dataProvider provideTestDetectLogLevel
     */
    public function testDetectLogLevel(string $level, int $expected): void
    {
        $conf = [
            "logger" => [
                "loglevel" => $level,
            ],
        ];
        $logger = $this->createLoggerByArray($conf);
        $this->assertSame($expected, $logger->getLogLevel());
    }

    /**
     * @return array
     */
    public function provideTestDetectLogLevel(): array
    {
        return [
            ["", Logger::LEVEL_ERROR],
            ["notfound", Logger::LEVEL_ERROR],
            ["Error", Logger::LEVEL_ERROR],
            ["alert", Logger::LEVEL_ALERT],
            ["INFO", Logger::LEVEL_INFO],
            ["deBug", Logger::LEVEL_DEBUG],
        ];
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutDataStorage(): void
    {
        $obj    = new StandardLoggerFactory();
        $conf   = new Config(new ArrayProperties(["logger" => []]));
        $logger = $obj->create($conf);
        $this->assertSame(Logger::getNopLogger(), $logger);
    }

    /**
     * @covers ::create
     */
    public function testCreateWithoutConfig(): void
    {
        $logger = $this->createLoggerByArray([]);
        $this->assertSame(Logger::getNopLogger(), $logger);
    }

    /**
     * @covers ::create
     */
    public function testCreateByDefault(): void
    {
        $storage = new DataLogStorage(new FileDataStorage($this->tmpdir), "logs/app", ".log");
        $format  = new DefaultLogFormat();
        $logger  = $this->createLoggerByArray(["logger" => []]);
        $this->assertEquals($storage, $logger->getStorage());
        $this->assertEquals($format, $logger->getFormat());
        $this->assertSame(Logger::LEVEL_ERROR, $logger->getLogLevel());
        $this->assertFalse($logger->isMultiple());
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $prop = [
            "logger" => [
                "dirname"  => "test1",
                "prefix"   => "sample",
                "loglevel" => "info",
                "multiple" => "yes",
                "format"   => "Y/m/d H:i:s",
            ],
        ];
        $storage = new DataLogStorage(new FileDataStorage($this->tmpdir), "test1/sample", ".log");
        $format  = new DefaultLogFormat("Y/m/d H:i:s");
        $logger  = $this->createLoggerByArray($prop);
        $this->assertEquals($storage, $logger->getStorage());
        $this->assertEquals($format, $logger->getFormat());
        $this->assertSame(Logger::LEVEL_INFO, $logger->getLogLevel());
        $this->assertTrue($logger->isMultiple());
    }
}
