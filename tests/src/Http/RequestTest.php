<?php

namespace Woof\Http;

use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Request
 */
class RequestTest extends TestCase
{
    /**
     * @return RequestBuilder
     */
    private function createTestBuilder(): RequestBuilder
    {
        $builder = new RequestBuilder();
        $builder->setHost("www.example.com");
        return $builder;
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     */
    public function testNewInstanceFail(): void
    {
        $this->expectException(LogicException::class);
        $builder = new RequestBuilder();
        $builder->build();
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHost
     */
    public function testGetHost(): void
    {
        $builder = new RequestBuilder();

        $obj1 = $builder->setHost("www.example.jp")->build();
        $this->assertSame("www.example.jp", $obj1->getHost());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getUri
     */
    public function testGetUri(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->build();
        $this->assertSame("", $obj1->getUri());
        $obj2 = $builder->setUri("/hoge/index.html?aaa=1")->build();
        $this->assertSame("/hoge/index.html?aaa=1", $obj2->getUri());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->build();
        $this->assertSame("", $obj1->getPath());
        $obj2 = $builder->setPath("/hoge/index.html")->build();
        $this->assertSame("/hoge/index.html", $obj2->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getScheme
     */
    public function testGetScheme(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->build();
        $this->assertSame("http", $obj1->getScheme());
        $obj2 = $builder->setScheme("HTTPS")->build();
        $this->assertSame("https", $obj2->getScheme());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getMethod
     */
    public function testGetMethod(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->build();
        $this->assertSame("get", $obj1->getMethod());
        $obj2 = $builder->setMethod("POST")->build();
        $this->assertSame("post", $obj2->getMethod());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::hasHeader
     */
    public function testHasHeader(): void
    {
        $builder = $this->createTestBuilder();

        $h1   = new TextField("X-TESTHEADER", "hogehoge");
        $obj1 = $builder->build();
        $this->assertFalse($obj1->hasHeader("X-TestHeader"));
        $obj2 = $builder->setHeader($h1)->build();
        $this->assertTrue($obj2->hasHeader("X-TestHeader"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHeader
     */
    public function testGetHeader(): void
    {
        $builder = $this->createTestBuilder();

        $h1   = new TextField("X-testheader", "hogehoge");
        $obj1 = $builder->build();
        $this->assertSame(EmptyField::getInstance(), $obj1->getHeader("X-TestHeader"));
        $obj2 = $builder->setHeader($h1)->build();
        $this->assertSame($h1, $obj2->getHeader("X-TestHeader"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getHeaderList
     */
    public function testGetHeaderList(): void
    {
        $builder = $this->createTestBuilder();

        $h1   = new TextField("X-Sample-Test01", "hogehoge");
        $h2   = new TextField("X-Sample-Test02", "fugafuga");
        $obj1 = $builder->build();
        $this->assertSame([], $obj1->getHeaderList());
        $obj2 = $builder->setHeader($h1)->setHeader($h2)->build();
        $this->assertSame([$h1, $h2], $obj2->getHeaderList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getQuery
     */
    public function testGetQuery(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->setQuery("hoge", "asdf")->build();
        $this->assertNull($obj1->getQuery("aaaa"));
        $this->assertSame("asdf", $obj1->getQuery("hoge"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getQueryList
     */
    public function testGetQueryList(): void
    {
        $builder = $this->createTestBuilder();

        $arr  = [
            "search" => "test",
            "mode"   => "1",
        ];
        $obj1 = $builder->setQueryList($arr)->build();
        $this->assertSame($arr, $obj1->getQueryList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getPost
     */
    public function testGetPost(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->setPost("fuga", "xxxx")->build();
        $this->assertNull($obj1->getPost("abcd"));
        $this->assertSame("xxxx", $obj1->getPost("fuga"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getPostList
     */
    public function testGetPostList(): void
    {
        $builder = $this->createTestBuilder();

        $arr  = [
            "content" => "This is a pen.",
            "process" => "confirm",
        ];
        $obj1 = $builder->setPostList($arr)->build();
        $this->assertSame($arr, $obj1->getPostList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getCookie
     */
    public function testGetCookie(): void
    {
        $builder = $this->createTestBuilder();

        $obj1 = $builder->setCookie("piyo", "yyyy")->build();
        $this->assertNull($obj1->getCookie("abcd"));
        $this->assertSame("yyyy", $obj1->getCookie("piyo"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getCookieList
     */
    public function testGetCookieList(): void
    {
        $builder = $this->createTestBuilder();

        $arr  = [
            "session_id" => "abcd1234",
            "ad_token"   => "aaaaaaaa",
        ];
        $obj1 = $builder->setCookieList($arr)->build();
        $this->assertSame($arr, $obj1->getCookieList());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getUploadFile
     */
    public function testGetUploadFile(): void
    {
        $builder = $this->createTestBuilder();

        $file = new UploadFile("sample.zip", TEST_DATA_DIR . "/tmp.zip", 0, 1234);
        $obj1 = $builder->setUploadFile("tmp", $file)->build();
        $this->assertSame($file, $obj1->getUploadFile("tmp"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::hasUploadFile
     */
    public function testHasUploadFile(): void
    {
        $builder = $this->createTestBuilder();

        $file = new UploadFile("tmp.zip", TEST_DATA_DIR . "/tmp.zip", 0, 1234);
        $obj1 = $builder->setUploadFile("tmp", $file)->build();
        $this->assertFalse($obj1->hasUploadFile("abc"));
        $this->assertTrue($obj1->hasUploadFile("tmp"));
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getUploadFile
     */
    public function testGetUploadFileFailWithFileNotFound(): void
    {
        $this->expectException(UploadFileNotFoundException::class);
        $builder = $this->createTestBuilder();

        $obj1 = $builder->build();
        $obj1->getUploadFile("notfound");
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getUploadFileList
     */
    public function testGetUploadFileList(): void
    {
        $builder = $this->createTestBuilder();

        $f1   = new UploadFile("sample.zip", TEST_DATA_DIR . "/sample.zip", 0, 1234);
        $f2   = new UploadFile("test01.png", TEST_DATA_DIR . "/test01.png", 0, 2345);
        $obj1 = $builder->setUploadFile("sample", $f1)->setUploadFile("test", $f2)->build();
        $this->assertSame(["sample" => $f1, "test" => $f2], $obj1->getUploadFileList());
    }
}
