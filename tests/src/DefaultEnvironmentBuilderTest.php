<?php

namespace Woof;

use PHPUnit\Framework\TestCase;
use Woof\Log\FileLogStorage;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;
use Woof\System\ArrayRandom;
use Woof\System\DefaultClock;
use Woof\System\DefaultRandom;
use Woof\System\FixedClock;
use Woof\System\Variables;
use Woof\System\VariablesBuilder;
use Woof\Util\FileProperties;

/**
 * @coversDefaultClass Woof\DefaultEnvironmentBuilder
 */
class DefaultEnvironmentBuilderTest extends TestCase
{
    /**
     * @var string
     */
    const TMP_DIR = TEST_DATA_DIR . "/DefaultEnvironmentBuilder/tmp";

    /**
     * @covers ::setConfig
     * @covers ::hasConfig
     * @covers ::getConfig
     */
    public function testConfig(): void
    {
        $conf = new Config(new FileProperties(self::TMP_DIR));
        $obj  = new DefaultEnvironmentBuilder();
        $this->assertFalse($obj->hasConfig());
        $this->assertSame($obj, $obj->setConfig($conf));
        $this->assertTrue($obj->hasConfig());
        $this->assertSame($conf, $obj->getConfig($conf));
    }

    /**
     * @covers ::setConfigDir
     * @covers ::getConfig
     */
    public function testSetConfigDir(): void
    {
        $tmp  = self::TMP_DIR;
        $conf = new Config(new FileProperties($tmp));
        $obj  = new DefaultEnvironmentBuilder();
        $this->assertSame($obj, $obj->setConfigDir($tmp));
        $this->assertEquals($conf, $obj->getConfig());
    }

    /**
     * @covers ::setResources
     * @covers ::hasResources
     * @covers ::getResources
     */
    public function testResources(): void
    {
        $res = new FileResources(self::TMP_DIR);
        $obj = new DefaultEnvironmentBuilder();
        $this->assertFalse($obj->hasResources());
        $this->assertSame(NullResources::getInstance(), $obj->getResources());
        $this->assertSame($obj, $obj->setResources($res));
        $this->assertTrue($obj->hasResources());
        $this->assertSame($res, $obj->getResources());
    }

    /**
     * @covers ::setResourcesDir
     * @covers ::getResources
     */
    public function testSetResourcesDir(): void
    {
        $tmp = self::TMP_DIR;
        $res = new FileResources($tmp);
        $obj = new DefaultEnvironmentBuilder();
        $this->assertSame($obj, $obj->setResourcesDir($tmp));
        $this->assertEquals($res, $obj->getResources());
    }

    /**
     * @covers ::setDataStorage
     * @covers ::hasDataStorage
     * @covers ::getDataStorage
     */
    public function testDataStorage(): void
    {
        $data = new FileDataStorage(self::TMP_DIR);
        $obj  = new DefaultEnvironmentBuilder();
        $this->assertFalse($obj->hasDataStorage());
        $this->assertSame($obj, $obj->setDataStorage($data));
        $this->assertTrue($obj->hasDataStorage());
        $this->assertSame($data, $obj->getDataStorage());
    }

    /**
     * @covers ::setDataStorageDir
     * @covers ::getDataStorage
     */
    public function testSetDataStorageDir(): void
    {
        $tmp  = self::TMP_DIR;
        $data = new FileDataStorage($tmp);
        $obj  = new DefaultEnvironmentBuilder();
        $this->assertSame($obj, $obj->setDataStorageDir($tmp));
        $this->assertEquals($data, $obj->getDataStorage());
    }

    /**
     * @covers ::setLogger
     * @covers ::hasLogger
     * @covers ::getLogger
     */
    public function testLogger(): void
    {
        $logger = (new LoggerBuilder())
            ->setStorage(new FileLogStorage(self::TMP_DIR))
            ->setLogLevel(Logger::LEVEL_INFO)
            ->build();
        $obj    = new DefaultEnvironmentBuilder();
        $this->assertFalse($obj->hasLogger());
        $this->assertSame($obj, $obj->setLogger($logger));
        $this->assertTrue($obj->hasLogger());
        $this->assertSame($logger, $obj->getLogger());
    }

    /**
     * @covers ::getLogger
     */
    public function testGetLoggerFirst(): void
    {
        $obj = new DefaultEnvironmentBuilder();
        $this->assertSame(Logger::getNopLogger(), $obj->getLogger());
    }

    /**
     * @covers ::setClock
     * @covers ::getClock
     */
    public function testClock(): void
    {
        $cl  = new FixedClock(1555555555);
        $obj = new DefaultEnvironmentBuilder();
        $this->assertSame(DefaultClock::getInstance(), $obj->getClock());
        $this->assertSame($obj, $obj->setClock($cl));
        $this->assertSame($cl, $obj->getClock());
    }

    /**
     * @covers ::setRandom
     * @covers ::getRandom
     */
    public function testRandom(): void
    {
        $rand = new ArrayRandom([123, 45678, 9012345]);
        $obj  = new DefaultEnvironmentBuilder();
        $this->assertSame(DefaultRandom::getInstance(), $obj->getRandom());
        $this->assertSame($obj, $obj->setRandom($rand));
        $this->assertSame($rand, $obj->getRandom());
    }

    /**
     * @covers ::setVariables
     * @covers ::hasVariables
     * @covers ::getVariables
     */
    public function testVariables(): void
    {
        $var = (new VariablesBuilder())->build();
        $obj = new DefaultEnvironmentBuilder();
        $this->assertFalse($obj->hasVariables());
        $this->assertSame(Variables::getDefaultInstance(), $obj->getVariables());
        $this->assertSame($obj, $obj->setVariables($var));
        $this->assertTrue($obj->hasVariables());
        $this->assertSame($var, $obj->getVariables());
    }
}
