<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;
use Woof\System\FixedClock;

/**
 * @coversDefaultClass Woof\Http\HttpDate
 */
class HttpDateTest extends TestCase
{
    /**
     * @var string
     */
    private $defaultTimezone;

    /**
     * @var HttpDateFormat
     */
    private $format;

    /**
     * このテストではタイムゾーンを Asia/Tokyo に固定します。
     */
    public function setUp(): void
    {
        $this->format          = new HttpDateFormat(new FixedClock(1600000000));
        $this->defaultTimezone = ini_set("timezone", "Asia/Tokyo");
    }

    /**
     * 固定したタイムゾーンを元の状態に戻します。
     */
    public function tearDown(): void
    {
        ini_set("timezone", $this->defaultTimezone);
    }

    /**
     * @covers ::__construct
     * @covers ::format
     */
    public function testFormat(): void
    {
        $obj = new HttpDate("Last-Modified", 1555555555, $this->format);
        $this->assertSame("Thu, 18 Apr 2019 02:45:55 GMT", $obj->format());
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $obj = new HttpDate("Last-Modified", 1555555555, $this->format);
        $this->assertSame("Last-Modified", $obj->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $obj = new HttpDate("Last-Modified", 1555555555, $this->format);
        $this->assertSame(1555555555, $obj->getValue());
    }
}
