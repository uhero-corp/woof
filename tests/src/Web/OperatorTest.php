<?php

namespace Woof\Web;

use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Http\ContentDisposition;
use Woof\Http\HttpDate;
use Woof\Http\Request;
use Woof\Http\RequestBuilder;
use Woof\Http\Response;
use Woof\Http\Response\Cookie;
use Woof\Http\Response\TextBody;
use Woof\Http\ResponseBuilder;
use Woof\Http\Status;
use Woof\Http\TextField;
use Woof\Resources;
use Woof\System\VariablesBuilder;

/**
 * @coversDefaultClass Woof\Web\Operator
 */
class OperatorTest extends TestCase
{
    /**
     * @var string
     */
    const TMP_DIR = TEST_DATA_DIR . "/Web/Operator/tmp";

    protected function setUp(): void
    {
        $tmpdir  = self::TMP_DIR;
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory(TEST_DATA_DIR . "/Web/Operator/subjects", $tmpdir);
    }

    /**
     * @return WebEnvironment
     */
    private function createTestWebEnvironment(): WebEnvironment
    {
        $server = [
            "HTTP_ACCEPT_ENCODING"   => "gzip, deflate",
            "HTTP_ACCEPT_LANGUAGE"   => "ja,en;q=0.75",
            "HTTP_DATE"              => "Sat, 24 Aug 2019 17:11:06 GMT",
            "HTTP_HOST"              => "www.example.com",
            "HTTP_IF_MODIFIED_SINCE" => "Thu, 18 Apr 2019 02:45:55 GMT",
            "HTTP_IF_NONE_MATCH"     => "abcdefabcdef",
            "HTTP_REFERER"           => "https://www.example.com/",
            "REMOTE_ADDR"            => "127.0.0.1",
            "REQUEST_URI"            => "/test/images/logo.png",
        ];
        $var = (new VariablesBuilder())
            ->setServer($server)
            ->build();

        $tmpdir = self::TMP_DIR;
        return (new WebEnvironmentBuilder())
            ->setConfigDir("{$tmpdir}/conf01")
            ->setResourcesDir("{$tmpdir}/res01")
            ->setDataStorageDir("{$tmpdir}/data01")
            ->setVariables($var)
            ->build();
    }

    /**
     * @param string $id
     * @return Request
     */
    private function createRequestBySessionId(string $id): Request
    {
        return (new RequestBuilder())
            ->setScheme("https")
            ->setHost("www.example.com")
            ->setPath("/test/sample/")
            ->setUri("/test/sample/")
            ->setCookie("test_sessid", $id)
            ->build();
    }

    /**
     * @return Request
     */
    private function createRequestWithCache(): Request
    {
        return (new RequestBuilder())
            ->setScheme("https")
            ->setHost("www.example.com")
            ->setPath("/test/css/style.css")
            ->setUri("/test/css/style.css")
            ->setHeader(new HttpDate("If-Modified-Since", 1555555555))
            ->setHeader(new TextField("If-None-Match", "abcdefabcdef"))
            ->build();
    }

    /**
     * @return Request
     */
    private function createRequestWithSession(): Request
    {
        return $this->createRequestBySessionId("1234567890abcdef");
    }

    /**
     * @return Request
     */
    private function createRequestWithoutSession(): Request
    {
        return $this->createRequestBySessionId("sessionidnotfound");
    }

    /**
     * @return Operator
     */
    private function createTestObject(): Operator
    {
        return new Operator($this->createRequestWithSession(), $this->createTestWebEnvironment());
    }

    /**
     * @return Operator
     */
    private function createTestObjectWithoutSession(): Operator
    {
        return new Operator($this->createRequestWithoutSession(), $this->createTestWebEnvironment());
    }

    /**
     * @return Operator
     */
    private function createTestObjectWithCache(): Operator
    {
        return new Operator($this->createRequestWithCache(), $this->createTestWebEnvironment());
    }

    /**
     * @covers ::__construct
     * @covers ::getResponseBuilder
     */
    public function testGetResponseBuilder(): void
    {
        $obj = $this->createTestObject();
        $this->assertEquals(new ResponseBuilder(), $obj->getResponseBuilder());
    }

    /**
     * 第 3 引数に Response を指定して Operator インスタンスを初期化した場合、
     * getResponseBuilder() で取得するオブジェクトにその Response
     * の情報が引継がれることを確認します。
     *
     * @covers ::__construct
     * @covers ::getResponseBuilder
     */
    public function testGetResponseBuilderByResponse(): void
    {
        $body = new TextBody("This is test");
        $res  = (new ResponseBuilder())
            ->setBody($body)
            ->build();

        $obj = new Operator($this->createRequestWithSession(), $this->createTestWebEnvironment(), $res);
        $this->assertSame($body, $obj->getResponseBuilder()->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::getSessionObject
     */
    public function testGetSessionObjectBySavedData(): void
    {
        $expected = new Session("1234567890abcdef", ["hoge" => 123, "fuga" => "asdf"]);
        $obj      = $this->createTestObject();
        $this->assertEquals($expected, $obj->getSessionObject());
    }

    /**
     * @covers ::__construct
     * @covers ::getSessionObject
     */
    public function testGetSessionObjectByNewData(): void
    {
        $obj     = $this->createTestObjectWithoutSession();
        $session = $obj->getSessionObject();
        $this->assertNotEquals("sessionidnotfound", $session->getId());
        $this->assertTrue($session->isNew());
        $this->assertTrue($session->isEmpty());
    }

    /**
     * @covers ::__construct
     * @covers ::setSession
     * @covers ::getSession
     */
    public function testSetSessionAndGetSession(): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($obj, $obj->setSession("var", "xxxx"));
        $this->assertSame("xxxx", $obj->getSession("var"));
    }

    /**
     * @covers ::__construct
     * @covers ::saveSession
     */
    public function testSaveSessionWithNewEmptyData(): void
    {
        $tmpdir = self::TMP_DIR;
        $obj    = $this->createTestObjectWithoutSession();
        $id     = $obj->getSessionObject()->getId();
        $this->assertSame($obj, $obj->saveSession());
        $this->assertFileNotExists("{$tmpdir}/data01/sessions/sess_{$id}");
    }

    /**
     * @covers ::__construct
     * @covers ::saveSession
     */
    public function testSaveSessionWithNewModifiedData(): void
    {
        $tmpdir = self::TMP_DIR;
        $obj    = $this->createTestObjectWithoutSession();
        $id     = $obj->getSessionObject()->getId();
        $this->assertSame($obj, $obj->setSession("var1", 135)->setSession("var2", "aaaa")->saveSession());

        $filename = "{$tmpdir}/data01/sessions/sess_{$id}";
        $expected = 'var1|i:135;var2|s:4:"aaaa";';
        $this->assertFileExists($filename);
        $this->assertSame($expected, file_get_contents($filename));

        $cookieList = $obj->getResponseBuilder()->getCookieList();
        $cookie     = $cookieList["test_sessid"];
        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertSame($id, $cookie->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::saveSession
     */
    public function testSaveSessionWithExistingData(): void
    {
        $tmpdir = self::TMP_DIR;
        $obj    = $this->createTestObject();
        $this->assertSame($obj, $obj->setSession("xxxx", true)->saveSession());

        $filename = "{$tmpdir}/data01/sessions/sess_1234567890abcdef";
        $expected = 'hoge|i:123;fuga|s:4:"asdf";xxxx|b:1;';
        $this->assertFileExists($filename);
        $this->assertSame($expected, file_get_contents($filename));

        $cookieList = $obj->getResponseBuilder()->getCookieList();
        $this->assertFalse(array_key_exists("test_sessid", $cookieList));
    }

    /**
     * @covers ::__construct
     * @covers ::setHeader
     */
    public function testSetHeader(): void
    {
        $obj = $this->createTestObject();
        $f   = new TextField("X-Testkey", "this is test");
        $this->assertSame($obj, $obj->setHeader($f));

        $headerList = $obj->getResponseBuilder()->getHeaderList();
        $this->assertSame($f, $headerList["x-testkey"]);
    }

    /**
     * @covers ::__construct
     * @covers ::setView
     */
    public function testSetView(): void
    {
        $view = new OperatorTest_TestView();
        $env  = $this->createTestWebEnvironment();
        $body = new ViewBody($view, $env->getResources(), $env->getContext());

        $obj = $this->createTestObject();
        $this->assertSame($obj, $obj->setView($view));
        $this->assertEquals($body, $obj->getResponseBuilder()->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::setBody
     */
    public function testSetBody(): void
    {
        $body = new TextBody("This is test");
        $obj  = $this->createTestObject();
        $this->assertSame($obj, $obj->setBody($body));
        $this->assertSame($body, $obj->getResponseBuilder()->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::setStatus
     */
    public function testSetStatus(): void
    {
        $status = Status::get403();
        $obj    = $this->createTestObject();
        $this->assertSame($obj, $obj->setStatus($status));
        $this->assertSame($status, $obj->getResponseBuilder()->getStatus());
    }

    /**
     * @covers ::__construct
     * @covers ::setCookie
     */
    public function testSetCookie(): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($obj, $obj->setCookie("testkey", "xxxx"));

        $cookieList = $obj->getResponseBuilder()->getCookieList();
        $cookie     = $cookieList["testkey"];
        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertSame("xxxx", $cookie->getValue());
    }

    /**
     * @param string $path
     * @param array $queryList
     * @param string $expected
     * @covers ::__construct
     * @covers ::formatAbsoluteUrl
     * @dataProvider provideTestFormatAbsoluteUrl
     */
    public function testFormatAbsoluteUrl(string $path, array $queryList, string $expected): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($expected, $obj->formatAbsoluteUrl($path, $queryList));
    }

    /**
     * @return array
     */
    public function provideTestFormatAbsoluteUrl(): array
    {
        return [
            ["/news.html", [], "https://www.example.com/test/news.html"],
            ["css/style.css", [], "https://www.example.com/test/css/style.css"],
            ["/search", ["q" => "test"], "https://www.example.com/test/search?q=test"],
            ["/inquiry?mode=submit", ["name" => "test"], "https://www.example.com/test/inquiry?mode=submit"],
            ["//www.example.org/xxxx/", [], "https://www.example.org/xxxx/"],
            ["http://www.example.org/.well-known/asdf", [], "http://www.example.org/.well-known/asdf"]
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::setRedirect
     */
    public function testSetRedirect(): void
    {
        $obj     = $this->createTestObject();
        $builder = $obj->getResponseBuilder();
        $this->assertFalse($builder->hasStatus());
        $this->assertSame($obj, $obj->setRedirect("/home", ["back" => 1]));
        $this->assertEquals(Status::get302(), $builder->getStatus());
        $headerList = $builder->getHeaderList();
        $this->assertEquals(new TextField("Location", "https://www.example.com/test/home?back=1"), $headerList["location"]);
    }

    /**
     * @covers ::__construct
     * @covers ::setRedirect
     */
    public function testSetRedirectAfterSetStatus(): void
    {
        $obj     = $this->createTestObject()->setStatus(Status::get301());
        $builder = $obj->getResponseBuilder();
        $this->assertTrue($builder->hasStatus());
        $this->assertSame($obj, $obj->setRedirect("/home", ["back" => 1]));
        $this->assertEquals(Status::get301(), $builder->getStatus());
    }

    /**
     *
     * @param Operator $obj
     * @param bool $expected
     * @covers ::checkNotModified
     */
    public function testCheckNotModified(): void
    {
        $time = 1555555555;
        $etag = "abcdefabcdef";
        $obj1 = $this->createTestObject();
        $obj2 = $this->createTestObjectWithCache();
        $this->assertFalse($obj1->checkNotModified($time, $etag));
        $this->assertTrue($obj2->checkNotModified($time, $etag));
    }

    /**
     * @covers ::__construct
     * @covers ::setAttachmentFilename
     */
    public function testSetAttachmentFilename(): void
    {
        $obj = $this->createTestObject();
        $this->assertSame($obj, $obj->setAttachmentFilename("sample data.zip"));

        $expected   = new ContentDisposition("sample data.zip");
        $headerList = $obj->getResponseBuilder()->getHeaderList();
        $this->assertEquals($expected, $headerList["content-disposition"]);
    }

    /**
     * @covers ::__construct
     * @covers ::build
     */
    public function testBuild(): void
    {
        $body = new TextBody("This is test");
        $obj  = $this->createTestObject();
        $res  = $obj->setBody($body)->build();
        $this->assertInstanceOf(Response::class, $res);
        $this->assertSame($body, $res->getBody());
    }
}

class OperatorTest_TestView implements View
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return "text/plain";
    }

    /**
     * @param Resources $resources
     * @param Context $context
     * @return string
     */
    public function render(Resources $resources, Context $context): string
    {
        return "Lorem Ipsum";
    }
}
