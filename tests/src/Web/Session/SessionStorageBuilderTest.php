<?php

namespace Woof\Web\Session;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Woof\System\ArrayRandom;
use Woof\System\DefaultClock;
use Woof\System\DefaultRandom;
use Woof\System\FixedClock;

/**
 * @coversDefaultClass Woof\Web\Session\SessionStorageBuilder
 */
class SessionStorageBuilderTest extends TestCase
{
    /**
     * @covers ::setSessionContainer
     * @covers ::hasSessionContainer
     * @covers ::getSessionContainer
     */
    public function testSessionContainer(): void
    {
        $container = new FileSessionContainer(TEST_DATA_DIR);
        $obj       = new SessionStorageBuilder();
        $this->assertFalse($obj->hasSessionContainer());
        $this->assertSame($obj, $obj->setSessionContainer($container));
        $this->assertTrue($obj->hasSessionContainer());
        $this->assertSame($container, $obj->getSessionContainer());
    }

    /**
     * @covers ::setKey
     * @covers ::getKey
     */
    public function testGetKeyAndSetKey(): void
    {
        $obj = new SessionStorageBuilder();
        $this->assertSame("", $obj->getKey());
        $this->assertSame($obj, $obj->setKey("sess_id"));
        $this->assertSame("sess_id", $obj->getKey());
    }

    /**
     * @covers ::setKey
     */
    public function testSetKeyFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new SessionStorageBuilder();
        $obj->setKey("invali session/key");
    }

    /**
     * @covers ::setMaxAge
     * @covers ::getMaxAge
     */
    public function testGetMaxAgeAndSetMaxAge(): void
    {
        $obj = new SessionStorageBuilder();
        $this->assertSame(1800, $obj->getMaxAge());
        $this->assertSame($obj, $obj->setMaxAge(900));
        $this->assertSame(900, $obj->getMaxAge());
    }

    /**
     * @covers ::setMaxAge
     */
    public function testSetMaxAgeFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new SessionStorageBuilder();
        $obj->setMaxAge(-300);
    }

    /**
     * @covers ::setGcProbability
     * @covers ::getGcProbability
     * @dataProvider provideTestSetGcProbabilityAndGetGcProbability
     */
    public function testSetGcProbabilityAndGetGcProbability(float $p): void
    {
        $obj = new SessionStorageBuilder();
        $this->assertSame(0.0, $obj->getGcProbability());
        $this->assertSame($obj, $obj->setGcProbability($p));
        $this->assertSame($p, $obj->getGcProbability());
    }

    /**
     * @return array
     */
    public function provideTestSetGcProbabilityAndGetGcProbability(): array
    {
        return [
            [0.25],
            [1.0],
            [0.0],
        ];
    }

    /**
     * @param float $p
     * @covers ::setGcProbability
     * @dataProvider provideTestSetGcProbabilityFail
     */
    public function testSetGcProbabilityFail(float $p): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new SessionStorageBuilder())->setGcProbability($p);
    }

    /**
     * @return array
     */
    public function provideTestSetGcProbabilityFail(): array
    {
        return [
            [-0.001],
            [1.001],
        ];
    }

    /**
     * @covers ::setClock
     * @covers ::getClock
     */
    public function testSetClockAndGetClock(): void
    {
        $clock = new FixedClock(1555555555);
        $obj   = new SessionStorageBuilder();
        $this->assertSame(DefaultClock::getInstance(), $obj->getClock());
        $this->assertSame($obj, $obj->setClock($clock));
        $this->assertSame($clock, $obj->getClock());
    }

    /**
     * @covers ::setRandom
     * @covers ::getRandom
     */
    public function testSetRandomAndGetRandom(): void
    {
        $random = new ArrayRandom([1, 2, 3]);
        $obj    = new SessionStorageBuilder();
        $this->assertSame(DefaultRandom::getInstance(), $obj->getRandom());
        $this->assertSame($obj, $obj->setRandom($random));
        $this->assertSame($random, $obj->getRandom());
    }

    /**
     * @covers ::build
     */
    public function testBuild(): void
    {
        $ss = (new SessionStorageBuilder())
            ->setKey("sess_id")
            ->setSessionContainer(new FileSessionContainer(TEST_DATA_DIR))
            ->build();
        $this->assertInstanceOf(SessionStorage::class, $ss);
    }
}
