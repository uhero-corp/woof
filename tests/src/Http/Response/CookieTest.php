<?php

namespace Woof\Http\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\Cookie
 */
class CookieTest extends TestCase
{
    /**
     * @var CookieAttributes
     */
    private $attributes;

    protected function setUp(): void
    {
        $this->attributes = (new CookieAttributesBuilder())
            ->setDomain("example.com")
            ->setPath("/test")
            ->setExpires(1555555555)
            ->setSecure(true)
            ->setHttpOnly(true)
            ->setSameSite("Lax")
            ->build();
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $obj = new Cookie("username", "john");
        $this->assertSame("username", $obj->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $obj = new Cookie("username", "john");
        $this->assertSame("john", $obj->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::getDomain
     */
    public function testGetDomain(): void
    {
        $obj1 = new Cookie("username", "john");
        $obj2 = new Cookie("username", "john", $this->attributes);
        $this->assertSame("", $obj1->getDomain());
        $this->assertSame("example.com", $obj2->getDomain());
    }

    /**
     * @covers ::__construct
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $obj1 = new Cookie("username", "john");
        $obj2 = new Cookie("username", "john", $this->attributes);
        $this->assertSame("", $obj1->getPath());
        $this->assertSame("/test", $obj2->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::getExpires
     */
    public function testGetExpires(): void
    {
        $obj1 = new Cookie("username", "john");
        $obj2 = new Cookie("username", "john", $this->attributes);
        $this->assertSame(0, $obj1->getExpires());
        $this->assertSame(1555555555, $obj2->getExpires());
    }

    /**
     * @covers ::__construct
     * @covers ::isSecure
     */
    public function testIsSecure(): void
    {
        $obj1 = new Cookie("username", "john");
        $obj2 = new Cookie("username", "john", $this->attributes);
        $this->assertSame(false, $obj1->isSecure());
        $this->assertSame(true, $obj2->isSecure());
    }

    /**
     * @covers ::__construct
     * @covers ::isHttpOnly
     */
    public function testIsHttpOnly(): void
    {
        $obj1 = new Cookie("username", "john");
        $obj2 = new Cookie("username", "john", $this->attributes);
        $this->assertSame(false, $obj1->isHttpOnly());
        $this->assertSame(true, $obj2->isHttpOnly());
    }

    /**
     * @covers ::__construct
     * @covers ::getSameSite
     */
    public function testGetSameSite(): void
    {
        $obj1 = new Cookie("username", "John");
        $obj2 = new Cookie("username", "John", $this->attributes);
        $this->assertSame("", $obj1->getSameSite());
        $this->assertSame("Lax", $obj2->getSameSite());
    }
}
