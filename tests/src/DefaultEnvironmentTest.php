<?php

namespace Woof;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Log\DataLogStorage;
use Woof\Log\FileLogStorage;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;
use Woof\System\ArrayRandom;
use Woof\System\Clock;
use Woof\System\DefaultClock;
use Woof\System\DefaultRandom;
use Woof\System\FixedClock;
use Woof\System\Random;
use Woof\System\Variables;
use Woof\System\VariablesBuilder;
use Woof\Util\FileProperties;

/**
 * @coversDefaultClass Woof\DefaultEnvironment
 */
class DefaultEnvironmentTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_DIR = TEST_DATA_DIR . "/DefaultEnvironment";

    public function setUp(): void
    {
        $tmpdir = self::TEST_DIR . "/tmp";
        $subdir = self::TEST_DIR . "/subjects";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory($subdir, $tmpdir);
    }

    /**
     * @return EnvironmentBuilder
     */
    private function createTestBuilder(): EnvironmentBuilder
    {
        $tmpdir = self::TEST_DIR . "/tmp";
        return (new DefaultEnvironmentBuilder())
            ->setConfigDir("{$tmpdir}/conf01")
            ->setResourcesDir("{$tmpdir}/res01")
            ->setDataStorageDir("{$tmpdir}/data01");
    }

    /**
     * Config がセットされていない EnvironmentBuilder の build() を実行した際に
     * LogicException をスローすることを確認します。
     *
     * @covers ::newInstance
     * @covers ::init
     */
    public function testNewInstanceFail(): void
    {
        $this->expectException(LogicException::class);
        (new DefaultEnvironmentBuilder())->build();
    }

    /**
     * @covers ::newInstance
     * @covers ::init
     */
    public function testNewInstance(): void
    {
        $obj = $this->createTestBuilder()->build();
        $this->assertInstanceOf(DefaultEnvironment::class, $obj);
    }

    /**
     * @covers ::newInstance
     * @covers ::getConfig
     */
    public function testGetConfig(): void
    {
        $expected = new Config(new FileProperties(self::TEST_DIR . "/tmp/conf01"));
        $obj      = $this->createTestBuilder()->build();
        $this->assertEquals($expected->getArray("app"), $obj->getConfig()->getArray("app"));
    }

    /**
     * @covers ::newInstance
     * @covers ::getResources
     */
    public function testGetResources(): void
    {
        $expected = new FileResources(self::TEST_DIR . "/tmp/res01");
        $obj      = $this->createTestBuilder()->build();
        $this->assertEquals($expected, $obj->getResources());
    }

    /**
     * @param DefaultEnvironment $obj
     * @param bool $expected
     * @covers ::hasDataStorage
     * @dataProvider provideTestHasDataStorage
     */
    public function testHasDataStorage(DefaultEnvironment $obj, bool $expected): void
    {
        $this->assertSame($expected, $obj->hasDataStorage());
    }

    /**
     * @return array
     */
    public function provideTestHasDataStorage(): array
    {
        $tmpdir  = self::TEST_DIR . "/tmp";
        $builder = (new DefaultEnvironmentBuilder())->setConfigDir("{$tmpdir}/conf01");
        $obj1    = $builder->build();
        $obj2    = $builder->setDataStorageDir("{$tmpdir}/data01")->build();
        return [
            [$obj1, false],
            [$obj2, true],
        ];
    }

    /**
     * @covers ::newInstance
     * @covers ::getDataStorage
     */
    public function testGetDataStorage(): void
    {
        $expected = new FileDataStorage(self::TEST_DIR . "/tmp/data01");
        $obj      = $this->createTestBuilder()->build();
        $this->assertEquals($expected, $obj->getDataStorage());
    }

    /**
     * DataStorage が設定されていない Environment オブジェクトの
     * getDataStorage() を実行した場合 LogicException をスローします。
     *
     * @covers ::newInstance
     * @covers ::getDataStorage
     */
    public function testGetDataStorageFail(): void
    {
        $this->expectException(LogicException::class);
        $tmpdir = self::TEST_DIR . "/tmp";
        $obj    = (new DefaultEnvironmentBuilder())->setConfigDir("{$tmpdir}/conf01")->build();
        $obj->getDataStorage();
    }

    /**
     * @param Environment $obj
     * @param Logger $expected
     * @covers ::getLogger
     * @dataProvider provideTestGetLogger
     */
    public function testGetLogger(Environment $obj, Logger $expected): void
    {
        $this->assertEquals($expected, $obj->getLogger());
    }

    /**
     * @return array
     */
    public function provideTestGetLogger(): array
    {
        $this->setUp();
        $tmpdir = self::TEST_DIR . "/tmp";

        $l1 = Logger::getNopLogger();
        $l2 = (new LoggerBuilder())
            ->setStorage(new DataLogStorage(new FileDataStorage("{$tmpdir}/data01"), "logdir/test"))
            ->setLogLevel(Logger::LEVEL_INFO)
            ->build();
        $l3 = (new LoggerBuilder())
            ->setLogLevel(Logger::LEVEL_DEBUG)
            ->setStorage(new FileLogStorage("{$tmpdir}/data01"))
            ->build();
        $b1 = (new DefaultEnvironmentBuilder())->setConfigDir("{$tmpdir}/conf01");
        $b2 = (new DefaultEnvironmentBuilder())->setConfigDir("{$tmpdir}/conf02");
        $b3 = (new DefaultEnvironmentBuilder())->setConfigDir("{$tmpdir}/conf01")->setLogger($l3);

        $obj1 = $b1->build();
        $obj2 = $b1->setDataStorageDir("{$tmpdir}/data01")->build();
        $obj3 = $b2->build();
        $obj4 = $b2->setDataStorageDir("{$tmpdir}/data01")->build();
        $obj5 = $b3->build();

        return [
            // logger の設定が存在しない場合は DataStorage の有無に関わらず NopLogger を返す
            [$obj1, $l1],
            [$obj2, $l1],
            // logger の設定が存在するが DataStorage が存在しない場合は NopLogger を返す
            [$obj3, $l1],
            // logger の設定と DataStorage が両方存在する場合は、設定に基づく Logger オブジェクトを返す
            [$obj4, $l2],
            // Builder に Logger オブジェクトが設定されている場合はそのオブジェクトを返す
            [$obj5, $l3],
        ];
    }

    /**
     *
     * @param Environment $obj
     * @param Clock $expected
     * @covers ::getClock
     * @dataProvider provideTestGetClock
     */
    public function testGetClock(Environment $obj, Clock $expected): void
    {
        $this->assertSame($expected, $obj->getClock());
    }

    /**
     * @return array
     */
    public function provideTestGetClock(): array
    {
        $c1      = new FixedClock(1555555555);
        $builder = $this->createTestBuilder();
        $obj1    = $builder->build();
        $obj2    = $builder->setClock($c1)->build();
        return [
            [$obj1, DefaultClock::getInstance()],
            [$obj2, $c1],
        ];
    }

    /**
     * @covers ::now
     */
    public function testNow(): void
    {
        $expected = 1555566666;
        $obj      = $this->createTestBuilder()->setClock(new FixedClock($expected))->build();
        $this->assertSame($expected, $obj->now());
    }

    /**
     * @param Environment $obj
     * @param Random $expected
     * @covers ::getRandom
     * @dataProvider provideTestGetRandom
     */
    public function testGetRandom(Environment $obj, Random $expected): void
    {
        $this->assertSame($expected, $obj->getRandom());
    }

    /**
     * @return array
     */
    public function provideTestGetRandom(): array
    {
        $r1      = new ArrayRandom([1, 2, 3]);
        $builder = $this->createTestBuilder();
        $obj1    = $builder->build();
        $obj2    = $builder->setRandom($r1)->build();
        return [
            [$obj1, DefaultRandom::getInstance()],
            [$obj2, $r1],
        ];
    }

    /**
     * @covers ::rand
     */
    public function testRandFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = $this->createTestBuilder()->build();
        $obj->rand(456, 123);
    }

    /**
     * @covers ::rand
     */
    public function testRand(): void
    {
        $rMax = mt_getrandmax();
        $seq  = [
            (int) ($rMax * 0.10),
            (int) ($rMax * 0.20),
            (int) ($rMax * 0.30),
            (int) ($rMax * 0.01),
            (int) ($rMax * 0.99),
            (int) ($rMax * 0.51),
        ];
        $obj  = $this->createTestBuilder()->setRandom(new ArrayRandom($seq))->build();

        $this->assertSame($seq[0], $obj->rand());
        $this->assertSame($seq[1], $obj->rand(234));
        $this->assertSame($seq[2], $obj->rand(null, 567));
        $this->assertSame(-4, $obj->rand(-4, 3));
        $this->assertSame(3, $obj->rand(-4, 3));
        $this->assertSame(0, $obj->rand(-4, 3));
    }

    /**
     * @param Environment $obj
     * @param Variables $expected
     * @covers ::getVariables
     * @dataProvider provideTestGetVariables
     */
    public function testGetVariables(Environment $obj, Variables $expected): void
    {
        $this->assertSame($expected, $obj->getVariables());
    }

    /**
     * @return array
     */
    public function provideTestGetVariables(): array
    {
        $v1      = (new VariablesBuilder())->build();
        $builder = $this->createTestBuilder();
        $obj1    = $builder->build();
        $obj2    = $builder->setVariables($v1)->build();
        return [
            [$obj1, Variables::getDefaultInstance()],
            [$obj2, $v1],
        ];
    }
}
