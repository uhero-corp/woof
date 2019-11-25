<?php

namespace Woof\Web\Session;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Http\Request;
use Woof\Http\RequestBuilder;
use Woof\System\ArrayRandom;
use Woof\System\FixedClock;

/**
 * @coversDefaultClass Woof\Web\Session\SessionStorage
 */
class SessionStorageTest extends TestCase
{
    /**
     *
     * @var string
     */
    private $tmpdir;

    protected function setUp(): void
    {
        $datadir = TEST_DATA_DIR . "/Web/Session/SessionStorage";
        $tmpdir  = "{$datadir}/tmp";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory("{$datadir}/subjects", $tmpdir);
        touch("{$tmpdir}/sess_1234567890abcdef", 1555555000);
        touch("{$tmpdir}/sess_1357924680bbbbbb", 1555550000);
        touch("{$tmpdir}/sess_9876543210aaaaaa", 1555555123);
        clearstatcache();

        $this->tmpdir = $tmpdir;
    }

    /**
     * @return SessionStorageBuilder
     */
    private function createTestBuilder(): SessionStorageBuilder
    {
        $clock = new FixedClock(1555555555);
        return (new SessionStorageBuilder())
            ->setSessionContainer(new FileSessionContainer($this->tmpdir, null, $clock))
            ->setKey("sess_id")
            ->setMaxAge(900)
            ->setClock($clock);
    }

    /**
     * @return SessionStorage
     */
    private function createTestObject(): SessionStorage
    {
        return $this->createTestBuilder()->build();
    }

    /**
     * @param string $sessionId
     * @return Request
     */
    private function createTestRequest(string $sessionId = ""): Request
    {
        $builder = (new RequestBuilder())->setHost("example.com");
        if (strlen($sessionId)) {
            $builder->setCookie("sess_id", $sessionId);
        }
        return $builder->build();
    }

    /**
     * SessionContainer と key がセットされていない SessionStorageBuilder
     * からインスタンスを生成しようとした場合 LogicException をスローします。
     *
     * @param SessionStorageBuilder $builder
     * @dataProvider provideTestNewInstanceFail
     * @covers ::__construct
     * @covers ::newInstance
     */
    public function testNewInstanceFail(SessionStorageBuilder $builder): void
    {
        $this->expectException(LogicException::class);
        $builder->build();
    }

    /**
     * @return array
     */
    public function provideTestNewInstanceFail(): array
    {
        return [
            [(new SessionStorageBuilder())->setKey("session_id")],
            [(new SessionStorageBuilder())->setSessionContainer(new FileSessionContainer(TEST_DATA_DIR))],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSessionContainer
     */
    public function testGetSessionContainer(): void
    {
        $expected = new FileSessionContainer($this->tmpdir, null, new FixedClock(1555555555));
        $this->assertEquals($expected, $this->createTestObject()->getSessionContainer());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getKey
     */
    public function testGetKey(): void
    {
        $this->assertSame("sess_id", $this->createTestObject()->getKey());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getMaxAge
     */
    public function testGetMaxAge(): void
    {
        $this->assertSame(900, $this->createTestObject()->getMaxAge());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getGcProbability
     */
    public function testGetGcProbability(): void
    {
        $obj = $this->createTestBuilder()->setGcProbability(0.125)->build();
        $this->assertSame(0.125, $obj->getGcProbability());
    }

    /**
     * 存在しないセッション ID を指定して getSession() を実行した場合、
     * 返される Session オブジェクトは下記の状態となります。
     *
     * - 新規フラグが true となっていること
     * - 引数に指定されたセッション ID とは違うセッション ID が新たに割り当てられること
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionByUnknownId(): void
    {
        $request = $this->createTestRequest("1234567890ffffff");
        $obj     = $this->createTestObject();
        $session = $obj->getSession($request);
        $this->assertTrue($session->isNew());
        $this->assertNotSame("1234567890ffffff", $session->getId());
    }

    /**
     * getSession() の引数に指定された HTTP リクエストのセッション ID が不正だった場合、新規セッションを生成して返します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionByInvalidRequest(): void
    {
        $request = $this->createTestRequest("this is invalid/session/key");
        $obj     = $this->createTestObject();
        $session = $obj->getSession($request);
        $this->assertTrue($session->isNew());
        $this->assertNotSame("this is invalid/session/key", $session->getId());
    }

    /**
     * 指定されたセッションのデータが破損している場合、返される Session オブジェクトのデータは空となります。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionByMalformedSession(): void
    {
        $request = $this->createTestRequest("9876543210aaaaaa");
        $obj     = $this->createTestObject();
        $session = $obj->getSession($request);
        $this->assertFalse($session->isNew());
        $this->assertSame([], $session->getAll());
    }

    /**
     * 生存中のセッション ID を指定して getSession() を実行した場合、返される Session オブジェクトは以下の状態となります。
     *
     * - 新規フラグが false となっていること
     * - セッション ID が引数に指定されたものに等しいこと
     * - 保存されているデータを保持していること
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionReturnsSavedSession(): void
    {
        $expected = [
            "hoge" => 123,
            "fuga" => "asdf",
        ];

        $request = $this->createTestRequest("1234567890abcdef");
        $obj     = $this->createTestObject();
        $session = $obj->getSession($request);
        $this->assertFalse($session->isNew());
        $this->assertSame("1234567890abcdef", $session->getId());
        $this->assertSame($expected, $session->getAll());
    }

    /**
     * 有効期限切れのセッション ID を指定して getSession() を実行した場合、セッション ID が新規発行されることを確認します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionByExpiredSession(): void
    {
        $request = $this->createTestRequest("1357924680bbbbbb");
        $obj     = $this->createTestObject();
        $session = $obj->getSession($request);
        $this->assertTrue($session->isNew());
        $this->assertNotSame("1357924680bbbbbb", $session->getId());
    }

    /**
     * 指定されたセッション ID を持つ Session オブジェクトが既に生成済みの場合、
     * SessionStorage 内にキャッシュされている Session オブジェクトを返すことを確認します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGetSessionReturnsCache(): void
    {
        $req1 = $this->createTestRequest();
        $obj  = $this->createTestObject();
        $s1   = $obj->getSession($req1);
        $id   = $s1->getId();

        $req2 = $this->createTestRequest($id);
        $s2   = $obj->getSession($req2);

        $this->assertSame($s1, $s2);
    }

    /**
     * 引数に指定された ID に紐づく Session オブジェクトが取得できることを確認します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSessionById
     */
    public function testGetSessionById(): void
    {
        $expected = [
            "hoge" => 123,
            "fuga" => "asdf",
        ];

        $obj     = $this->createTestObject();
        $session = $obj->getSessionById("1234567890abcdef");
        $this->assertFalse($session->isNew());
        $this->assertSame("1234567890abcdef", $session->getId());
        $this->assertSame($expected, $session->getAll());
    }

    /**
     * 引数に指定された ID のセッションが存在しない場合、
     * 指定された ID を持つセッションを新規作成します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSessionById
     * @covers ::<private>
     */
    public function testGetSessionByIdByUnknownId(): void
    {
        $obj     = $this->createTestObject();
        $session = $obj->getSessionById("1234567890ffffff");
        $this->assertTrue($session->isNew());
        $this->assertSame("1234567890ffffff", $session->getId());
        $this->assertTrue($session->isEmpty());
    }

    /**
     * 引数に指定された ID のセッションが有効期限切れの場合、
     * 指定された ID を持つセッションを新規作成します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSessionById
     * @covers ::<private>
     */
    public function testGetSessionByIdByExpiredId(): void
    {
        $obj     = $this->createTestObject();
        $session = $obj->getSessionById("1357924680bbbbbb");
        $this->assertTrue($session->isNew());
        $this->assertSame("1357924680bbbbbb", $session->getId());
        $this->assertTrue($session->isEmpty());
    }

    /**
     * 不正なセッション ID を指定して getSessionById() を実行した場合、
     * InvalidArgumentException をスローすることを確認します。
     *
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSessionById
     */
    public function testGetSessionByIdFail()
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = $this->createTestObject();
        $obj->getSessionById("invalid session id");
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::save
     * @covers ::<private>
     */
    public function testSave()
    {
        $request  = $this->createTestRequest("1234567890abcdef");
        $expected = 'hoge|i:456;fuga|s:4:"asdf";piyo|b:1;';
        $obj      = $this->createTestObject();
        $session  = $obj->getSession($request);
        $session->set("hoge", 456);
        $session->set("piyo", true);
        $obj->save($session);
        $this->assertSame($expected, file_get_contents("{$this->tmpdir}/sess_1234567890abcdef"));
    }

    /**
     * セッションを取得する際に一定の確率で期限切れセッションの消去を行うことを確認します。
     *
     * @param float $gcProbability
     * @param bool $expected
     * @dataProvider provideTestGarbageCorrectionExecuted
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getSession
     * @covers ::<private>
     */
    public function testGarbageCorrectionExecuted(float $gcProbability, bool $expected): void
    {
        $rand = new ArrayRandom([(int) (mt_getrandmax() * 0.5)]);
        $obj  = $this->createTestBuilder()
            ->setRandom($rand)
            ->setGcProbability($gcProbability)
            ->build();
        $req  = $this->createTestRequest("xxxxxxxxxxxxxxxx");
        $obj->getSession($req);
        $this->assertFileExists("{$this->tmpdir}/sess_1234567890abcdef");
        $this->assertSame($expected, file_exists("{$this->tmpdir}/sess_1357924680bbbbbb"));
    }

    /**
     * @return array
     */
    public function provideTestGarbageCorrectionExecuted(): array
    {
        return [
            [1.0, false],
            [0.0, true],
            [0.25, true],
            [0.75, false],
        ];
    }
}
