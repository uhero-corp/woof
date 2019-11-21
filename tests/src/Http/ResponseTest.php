<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;
use Woof\Http\Response\Cookie;
use Woof\Http\Response\CookieAttributesBuilder;
use Woof\Http\Response\EmptyBody;
use Woof\Http\Response\TextBody;

/**
 * @coversDefaultClass Woof\Http\Response
 */
class ResponseTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getBody
     */
    public function testGetBody(): void
    {
        $body    = new TextBody("This is test");
        $builder = new ResponseBuilder();
        $obj1    = $builder->build();
        $this->assertSame(EmptyBody::getInstance(), $obj1->getBody());
        $obj2    = $builder->setBody($body)->build();
        $this->assertSame($body, $obj2->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getStatus
     */
    public function testGetStatus(): void
    {
        $status  = Status::get404();
        $builder = new ResponseBuilder();
        $obj1    = $builder->build();
        $this->assertEquals(Status::getOK(), $obj1->getStatus());
        $obj2    = $builder->setStatus($status)->build();
        $this->assertSame($status, $obj2->getStatus());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::hasHeader
     */
    public function testHasHeader(): void
    {
        $builder = new ResponseBuilder();
        $obj1    = $builder->build();
        $this->assertFalse($obj1->hasHeader("x-testheader"));
        $obj2    = $builder->setHeader(new TextField("X-TestHeader", "abc"))->build();
        $this->assertTrue($obj2->hasHeader("x-testheader"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHeader
     */
    public function testGetHeader(): void
    {
        $h1      = new HttpDate("Last-Modified", 1555555555);
        $builder = new ResponseBuilder();
        $obj1    = $builder->build();
        $this->assertSame(EmptyField::getInstance(), $obj1->getHeader("Last-modified"));
        $obj2    = $builder->setHeader($h1)->build();
        $this->assertSame($h1, $obj2->getHeader("Last-modified"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHeaderList
     */
    public function testGetHeaderList(): void
    {
        $h1       = new TextField("ETag", "abcdef1234567890");
        $h2       = new HttpDate("Last-Modified", 1555555555);
        $h3       = new ContentDisposition("sample.txt");
        $expected = [$h1, $h2, $h3];
        $builder  = new ResponseBuilder();
        $obj      = $builder->setHeader($h1)->setHeader($h2)->setHeader($h3)->build();
        $this->assertEquals($expected, $obj->getHeaderList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHeaderList
     */
    public function testGetHeaderListForBody(): void
    {
        $body     = new TextBody("1234567890abc", "text/plain");
        $h1       = new HttpDate("Last-Modified", 1543210987);
        $h2       = new TextField("Content-Type", "text/plain");
        $h3       = new TextField("Content-Length", "13");
        $expected = [$h1, $h2, $h3];
        $builder  = new ResponseBuilder();
        $obj      = $builder->setBody($body)->setHeader($h1)->build();
        $this->assertEquals($expected, $obj->getHeaderList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getCookieList
     */
    public function testGetCookieList(): void
    {
        $attr = (new CookieAttributesBuilder())->setDomain("www.example.com")->build();

        $expected = [
            "session_id" => new Cookie("session_id", "abcdabcd12345678", $attr),
            "a"          => new Cookie("a", "xxxx", $attr),
            "b"          => new Cookie("b", "yyyy", $attr),
        ];

        $obj = (new ResponseBuilder())
            ->setCookie("session_id", "abcdabcd12345678", $attr)
            ->setCookie("a", "xxxx", $attr)
            ->setCookie("b", "yyyy", $attr)
            ->build();
        $this->assertEquals($expected, $obj->getCookieList());
    }
}
