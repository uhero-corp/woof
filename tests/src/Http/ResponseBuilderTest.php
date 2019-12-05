<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;
use Woof\Http\Response\Cookie;
use Woof\Http\Response\EmptyBody;
use Woof\Http\Response\TextBody;

/**
 * @coversDefaultClass Woof\Http\ResponseBuilder
 */
class ResponseBuilderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setBody
     * @covers ::getBody
     */
    public function testSetBodyAndGetBody(): void
    {
        $obj  = new ResponseBuilder();
        $body = new TextBody("This is test", "text/plain");
        $this->assertSame(EmptyBody::getInstance(), $obj->getBody());
        $this->assertSame($obj, $obj->setBody($body));
        $this->assertSame($body, $obj->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::setStatus
     * @covers ::getStatus
     */
    public function testSetStatusAndGetStatus(): void
    {
        $obj    = new ResponseBuilder();
        $status = Status::get404();
        $this->assertEquals(Status::getOK(), $obj->getStatus());
        $this->assertSame($obj, $obj->setStatus($status));
        $this->assertSame($status, $obj->getStatus());
    }

    /**
     * @covers ::__construct
     * @covers ::hasStatus
     */
    public function testHasStatus(): void
    {
        $obj = new ResponseBuilder();
        $this->assertFalse($obj->hasStatus());
        $obj->setStatus(Status::get302());
        $this->assertTrue($obj->hasStatus());
    }

    /**
     *
     * @covers ::__construct
     * @covers ::setHeader
     * @covers ::getHeaderList
     */
    public function testSetHeaderAndGetHeaders(): void
    {
        $h1  = new TextField("ETag", "1234567890abcdef");
        $h2  = new HttpDate("Last-Modified", 1555555555);
        $obj = new ResponseBuilder();

        $expected = ["etag" => $h1, "last-modified" => $h2];
        $this->assertSame($obj, $obj->setHeader($h1)->setHeader($h2)->setHeader(EmptyField::getInstance()));
        $this->assertEquals($expected, $obj->getHeaderList());
    }

    /**
     * @covers ::__construct
     * @covers ::setCookie
     * @covers ::getCookieList
     */
    public function testSetCookieAndGetCookies(): void
    {
        $obj = new ResponseBuilder();

        $expected = [
            "var1" => new Cookie("var1", "hoge"),
            "var2" => new Cookie("var2", "fuga"),
        ];
        $this->assertSame([], $obj->getCookieList());
        $this->assertSame($obj, $obj->setCookie("var1", "hoge")->setCookie("var2", "fuga"));
        $this->assertEquals($expected, $obj->getCookieList());
    }

    /**
     * @covers ::__construct
     * @covers ::build
     */
    public function testBuild(): void
    {
        $obj = new ResponseBuilder();
        $this->assertInstanceOf(Response::class, $obj->build());
    }

    /**
     * @covers ::__construct
     * @covers ::importResponse
     */
    public function testConstructByResponse(): void
    {
        $obj = (new ResponseBuilder())
            ->setBody(new TextBody("This is test", "text/plain"))
            ->setStatus(Status::get400())
            ->setCookie("session_id", "abcdabcd12341234")
            ->setHeader(new TextField("X-Test-Header", "TEST"));
        $res = $obj->build();
        $this->assertEquals($obj, new ResponseBuilder($res));
    }
}
