<?php

namespace Woof\Http\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\CookieAttributesBuilder
 */
class CookieAttributesBuilderTest extends TestCase
{
    /**
     * @covers ::setDomain
     * @covers ::getDomain
     */
    public function testSetDomainAndGetDomain(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setDomain("example.com"));
        $this->assertSame("example.com", $obj->getDomain());
    }

    /**
     * @covers ::setPath
     * @covers ::getPath
     */
    public function testSetPathAndGetPath(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setPath("/test"));
        $this->assertSame("/test", $obj->getPath());
    }

    /**
     * @covers ::setExpires
     * @covers ::getExpires
     */
    public function testSetExpiresAndGetExpires(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setExpires(1555555555));
        $this->assertSame(1555555555, $obj->getExpires());
    }

    /**
     * @covers ::setSecure
     * @covers ::isSecure
     */
    public function testSetSecureAndIsSecure(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setSecure(true));
        $this->assertTrue($obj->isSecure());
    }

    /**
     * @covers ::setHttpOnly
     * @covers ::isHttpOnly
     */
    public function testSetHttpOnlyAndIsHttpOnly(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setHttpOnly(true));
        $this->assertTrue($obj->isHttpOnly());
    }

    /**
     * @param string $value
     * @dataProvider provideTestSetSameSiteAndGetSameSite
     * @covers ::setSameSite
     * @covers ::getSameSite
     */
    public function testSetSameSiteAndGetSameSite(string $value, string $expected): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertSame($obj, $obj->setSameSite($value));
        $this->assertSame($expected, $obj->getSameSite());
    }

    /**
     * @return array
     */
    public function provideTestSetSameSiteAndGetSameSite(): array
    {
        return [
            ["", ""],
            ["STRICT", "Strict"],
            ["lax", "Lax"],
            ["None", "None"],
        ];
    }

    /**
     * @covers ::setSameSite
     */
    public function testSetSameSiteFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new CookieAttributesBuilder())->setSameSite("Invalid");
    }

    /**
     * @return ::build
     */
    public function testBuild(): void
    {
        $obj = new CookieAttributesBuilder();
        $this->assertInstanceOf(CookieAttributes::class, $obj->build());
    }
}
