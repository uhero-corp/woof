<?php

namespace Woof\Http\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\CookieAttributes
 */
class CookieAttributesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getDomain
     */
    public function testGetDomain(): void
    {
        $obj = (new CookieAttributesBuilder())->setDomain("example.com")->build();
        $this->assertSame("example.com", $obj->getDomain());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $obj = (new CookieAttributesBuilder())->setPath("/test")->build();
        $this->assertSame("/test", $obj->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getExpires
     */
    public function testGetExpires(): void
    {
        $obj = (new CookieAttributesBuilder())->setExpires(1500000000)->build();
        $this->assertSame(1500000000, $obj->getExpires());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::isSecure
     */
    public function testIsSecure(): void
    {
        $obj = (new CookieAttributesBuilder())->setSecure(true)->build();
        $this->assertTrue($obj->isSecure());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::isHttpOnly
     */
    public function testIsHttpOnly(): void
    {
        $obj = (new CookieAttributesBuilder())->setHttpOnly(true)->build();
        $this->assertTrue($obj->isHttpOnly());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSameSite
     */
    public function testGetSameSite(): void
    {
        $obj = (new CookieAttributesBuilder())->setSameSite("lax")->build();
        $this->assertSame("Lax", $obj->getSameSite());
    }
    /**
     * @covers ::__construct
     * @covers ::newInstance
     */
    public function testDefaultInstance(): void
    {
        $obj = (new CookieAttributesBuilder())->build();
        $this->assertSame("", $obj->getDomain());
        $this->assertSame("", $obj->getPath());
        $this->assertSame(0, $obj->getExpires());
        $this->assertFalse($obj->isSecure());
        $this->assertFalse($obj->isHttpOnly());
    }
}
