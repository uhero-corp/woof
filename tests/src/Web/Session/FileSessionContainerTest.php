<?php

namespace Woof\Web\Session;

use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Log\FileLogStorage;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;
use Woof\System\FileSystemException;
use Woof\System\FixedClock;

/**
 * @coversDefaultClass Woof\Web\Session\FileSessionContainer
 */
class FileSessionContainerTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpdir;

    /**
     * @var string
     */
    private $logdir;

    /**
     * @var string
     */
    private $defaultTimezone;

    /**
     * 擬似的なセッション保存領域を作成します。
     * また、テストのためタイムゾーンを Asia/Tokyo に固定します。
     */
    protected function setUp(): void
    {
        $datadir = TEST_DATA_DIR . "/Web/Session/FileSessionContainer";
        $tmpdir  = "{$datadir}/tmp";
        $logdir  = "{$datadir}/logs";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::cleanDirectory($logdir);
        TestHelper::copyDirectory("{$datadir}/subjects", $tmpdir);
        touch("{$tmpdir}/sess_1234567890abcdef", 1500009000);
        touch("{$tmpdir}/sess_1357924680bbbbbb", 1500005000);
        touch("{$tmpdir}/sess_9876543210aaaaaa", 1500008000);

        $this->tmpdir = $tmpdir;
        $this->logdir = $logdir;

        $this->defaultTimezone = ini_set("date.timezone", "Asia/Tokyo");
    }

    /**
     * 固定したタイムゾーンを元の状態に戻します。
     */
    protected function tearDown(): void
    {
        ini_set("date.timezone", $this->defaultTimezone);
    }

    /**
     * @return Logger
     */
    private function getLogger(): Logger
    {
        return (new LoggerBuilder())
            ->setClock(new FixedClock(1500010000))
            ->setStorage(new FileLogStorage($this->logdir))
            ->setLogLevel(Logger::LEVEL_ALERT)
            ->build();
    }

    /**
     * @covers ::__construct
     */
    public function testConstructFailByInvalidDirectory(): void
    {
        $this->expectException(FileSystemException::class);
        new FileSessionContainer("{$this->tmpdir}/notfound");
    }

    /**
     * @param int $maxAge
     * @param int $expected
     * @covers ::__construct
     * @covers ::cleanExpiredSessions
     * @dataProvider provideTestCleanExpiredSessions
     */
    public function testCleanExpiredSessions(int $maxAge, int $expected): void
    {
        $obj = new FileSessionContainer($this->tmpdir, null, new FixedClock(1500010000));
        $this->assertSame($expected, $obj->cleanExpiredSessions($maxAge));
    }

    /**
     * @return array
     */
    public function provideTestCleanExpiredSessions(): array
    {
        return [
            [7200, 0],
            [1800, 2],
            [3600, 1],
        ];
    }

    /**
     * @param string $id
     * @param int $maxAge
     * @param bool $expected
     * @dataProvider provideTestContains
     */
    public function testContains(string $id, int $maxAge, bool $expected): void
    {
        $obj = new FileSessionContainer($this->tmpdir, null, new FixedClock(1500010000));
        $this->assertSame($expected, $obj->contains($id, $maxAge));
    }

    /**
     * @return array
     */
    public function provideTestContains(): array
    {
        return [
            ["1234567890abcdef", 1800, true],
            ["9876543210aaaaaa", 1800, false],
            ["9876543210aaaaaa", 3600, true],
            ["xxxxxxxxxxxxxxxx", 1800, false],
        ];
    }

    /**
     * @param string $id
     * @param array $expected
     * @covers ::__construct
     * @covers ::load
     * @dataProvider provideTestLoadSuccess
     */
    public function testLoadSuccess(string $id, array $expected): void
    {
        $obj = new FileSessionContainer($this->tmpdir, null, new FixedClock(1500010000));
        $this->assertSame($expected, $obj->load($id));

        $filename = "{$this->tmpdir}/sess_{$id}";
        clearstatcache(true, $filename);
        $this->assertSame(1500010000, filemtime($filename));
    }

    /**
     * @return array
     */
    public function provideTestLoadSuccess(): array
    {
        return [
            ["1234567890abcdef", ["hoge" => 123, "fuga" => "asdf"]],
            ["1357924680bbbbbb", ["a" => ["x", "yy", "zzz"], "b" => ["hoge" => 12, "fuga" => 345]]],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::load
     */
    public function testLoadFailByNotExistingId(): void
    {
        $obj = new FileSessionContainer($this->tmpdir);
        $this->assertSame([], $obj->load("xxxxxxxxxxxxxxxx"));
    }

    /**
     * @covers ::__construct
     * @covers ::load
     */
    public function testLoadFailByInvalidFormat(): void
    {
        $obj = new FileSessionContainer($this->tmpdir, $this->getLogger());
        $this->assertSame([], $obj->load("9876543210aaaaaa"));

        $expectedLog = "[2017-07-14 14:26:40][ALERT] Failed to parse session for ID '9876543210aaaaaa'";
        $file        = file("{$this->logdir}/app-20170714.log", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $this->assertSame($expectedLog, $file[0]);
    }

    /**
     * @covers ::save
     * @covers ::<private>
     */
    public function testSave(): void
    {
        $obj      = new FileSessionContainer($this->tmpdir);
        $expected = 'hoge|i:456;fuga|s:4:"asdf";piyo|b:1;';
        $result   = $obj->save("1234567890abcdef", ["hoge" => 456, "fuga" => "asdf", "piyo" => true]);
        $this->assertTrue($result);
        $this->assertSame($expected, trim(file_get_contents("{$this->tmpdir}/sess_1234567890abcdef")));
    }

    /**
     * save() が失敗した時の挙動をテストします。下記を確認します。
     *
     * - 返り値が false になること
     * - 保存に失敗した旨の ALERT ログが書き込まれること
     *
     * @covers ::save
     * @covers ::<private>
     */
    public function testSaveFail(): void
    {
        set_error_handler(function () {});

        // 存在しないディレクトリをセッションの保存先とする FileSessionContainer を作成します
        $delDir = "{$this->tmpdir}/deldir";
        mkdir($delDir);
        $obj    = new FileSessionContainer($delDir, $this->getLogger());
        rmdir($delDir);

        $result      = $obj->save("1234567890abcdef", ["hoge" => 456, "fuga" => "asdf", "piyo" => true]);
        $this->assertFalse($result);
        $expectedLog = "[2017-07-14 14:26:40][ALERT] Failed to save session to '{$delDir}/sess_1234567890abcdef'";
        $this->assertSame($expectedLog, trim(file_get_contents("{$this->logdir}/app-20170714.log")));

        restore_error_handler();
    }
}
