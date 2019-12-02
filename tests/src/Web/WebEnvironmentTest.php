<?php

namespace Woof\Web;

use LogicException;
use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Config;
use Woof\FileDataStorage;
use Woof\Http\HttpDate;
use Woof\Http\QualityValues;
use Woof\Http\TextField;
use Woof\Log\DataLogStorage;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;
use Woof\System\VariablesBuilder;
use Woof\Util\ArrayProperties;
use Woof\Web\Session\FileSessionContainer;
use Woof\Web\Session\SessionStorage;
use Woof\Web\Session\SessionStorageBuilder;

/**
 * @coversDefaultClass Woof\Web\WebEnvironment
 */
class WebEnvironmentTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_DIR = TEST_DATA_DIR . "/Web/WebEnvironment";

    public function setUp(): void
    {
        $tmpdir = self::TEST_DIR . "/tmp";
        $subdir = self::TEST_DIR . "/subjects";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory($subdir, $tmpdir);
    }

    /**
     * @return WebEnvironmentBuilder
     */
    private function createEmptyBuilder(): WebEnvironmentBuilder
    {
        $server = [
            "HTTP_ACCEPT_ENCODING"   => "gzip, deflate",
            "HTTP_ACCEPT_LANGUAGE"   => "ja,en-US;q=0.9,en;q=0.8",
            "HTTP_DATE"              => "Sat, 24 Aug 2019 17:11:06 GMT",
            "HTTP_HOST"              => "www.example.com",
            "HTTP_IF_MODIFIED_SINCE" => "Thu, 18 Apr 2019 02:45:55 GMT",
            "HTTP_IF_NONE_MATCH"     => "abcdefabcdef",
            "HTTP_REFERER"           => "https://www.example.com/",
            "REMOTE_ADDR"            => "127.0.0.1",
            "REQUEST_URI"            => "/app01/css/style.css",
        ];
        $var = (new VariablesBuilder())
            ->setServer($server)
            ->build();
        return (new WebEnvironmentBuilder())->setVariables($var);
    }

    /**
     * @return WebEnvironmentBuilder
     */
    private function createTestBuilder(): WebEnvironmentBuilder
    {
        $tmpdir = self::TEST_DIR . "/tmp";
        return $this->createEmptyBuilder()
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
        $this->createEmptyBuilder()->build();
    }

    /**
     * @covers ::newInstance
     * @covers ::init
     * @covers ::<private>
     */
    public function testNewInstance(): void
    {
        $obj = $this->createTestBuilder()->build();
        $this->assertInstanceOf(WebEnvironment::class, $obj);
    }

    /**
     * @param WebEnvironment $obj
     * @param Context $expected
     * @covers ::getContext
     * @dataProvider provideTestGetContext
     */
    public function testGetContext(WebEnvironment $obj, Context $expected): void
    {
        $this->assertEquals($expected, $obj->getContext());
    }

    /**
     * @return array
     */
    public function provideTestGetContext(): array
    {
        $this->setUp();
        $tmpdir = self::TEST_DIR . "/tmp";

        $c1 = new Context("/app01", ":");
        $c2 = new Context("/");

        $obj1 = $this->createEmptyBuilder()->setConfigDir("{$tmpdir}/conf01")->build();
        $obj2 = $this->createEmptyBuilder()->setConfigDir("{$tmpdir}/conf02")->build();

        return [
            [$obj1, $c1],
            [$obj2, $c2],
        ];
    }

    /**
     * @param WebEnvironment $obj
     * @param SessionStorage $expected
     * @covers ::getSessionStorage
     * @dataProvider provideTestGetSessionStorage
     */
    public function testGetSessionStorage(WebEnvironment $obj, SessionStorage $expected): void
    {
        $this->assertEquals($expected, $obj->getSessionStorage());
    }

    /**
     * @return array
     */
    public function provideTestGetSessionStorage(): array
    {
        $this->setUp();
        $tmpdir = self::TEST_DIR . "/tmp";

        $logger = (new LoggerBuilder())
            ->setLogLevel(Logger::LEVEL_INFO)
            ->setStorage(new DataLogStorage(new FileDataStorage("{$tmpdir}/data01"), "logdir/test"))
            ->build();

        $ss1 = (new StandardSessionStorageFactory())
            ->create(new Config(new ArrayProperties([])));
        $ss2 = (new SessionStorageBuilder())
            ->setSessionContainer(new FileSessionContainer("{$tmpdir}/data01/sess01", $logger))
            ->setKey("testkey")
            ->setMaxAge(900)
            ->setGcProbability(0.125)
            ->build();
        $ss3 = (new SessionStorageBuilder())
            ->setSessionContainer(new FileSessionContainer("{$tmpdir}/data01/sess02"))
            ->setKey("asdf")
            ->setMaxAge(600)
            ->build();

        $obj1 = $this->createEmptyBuilder()
            ->setConfigDir("{$tmpdir}/conf01")
            ->build();
        $obj2 = $this->createEmptyBuilder()
            ->setConfigDir("{$tmpdir}/conf02")
            ->setDataStorageDir("{$tmpdir}/data01")
            ->build();
        $obj3 = $this->createEmptyBuilder()
            ->setSessionStorage($ss3)
            ->setConfigDir("{$tmpdir}/conf01")
            ->build();

        return [
            // session の設定が存在しない場合はデフォルトの SessionStorage を返す
            [$obj1, $ss1],
            // session の設定が存在する場合は設定値に基づく SessionStorage を返す
            [$obj2, $ss2],
            // Builder に SessionStorage が直接指定されている場合はそのオブジェクトを返す
            [$obj3, $ss3],
        ];
    }

    /**
     * @covers ::getClientRequest
     * @covers ::<private>
     */
    public function testGetClientRequest(): void
    {
        $h1  = new QualityValues("Accept-Language", ["ja" => 1, "en-US" => 0.9, "en" => 0.8]);
        $h2  = new HttpDate("If-Modified-Since", 1555555555);
        $h3  = new TextField("Referer", "https://www.example.com/");
        $obj = $this->createTestBuilder()->build();
        $req = $obj->getClientRequest();
        $this->assertSame("www.example.com", $req->getHost());
        $this->assertSame("/app01/css/style.css", $req->getPath());
        $this->assertEquals($h1, $req->getHeader("accept-language"));
        $this->assertEquals($h2, $req->getHeader("if-modified-since"));
        $this->assertEquals($h3, $req->getHeader("referer"));
    }
}
