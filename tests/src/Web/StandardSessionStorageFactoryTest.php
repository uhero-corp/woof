<?php

namespace Woof\Web;

use PHPUnit\Framework\TestCase;
use TestHelper;
use Woof\Config;
use Woof\FileDataStorage;
use Woof\Log\FileLogStorage;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;
use Woof\Util\ArrayProperties;
use Woof\Web\Session\FileSessionContainer;
use Woof\Web\Session\SessionStorage;
use Woof\Web\Session\SessionStorageBuilder;

/**
 * @coversDefaultClass Woof\Web\StandardSessionStorageFactory
 */
class StandardSessionStorageFactoryTest extends TestCase
{
    /**
     * @var string
     */
    const TMP_DIR = TEST_DATA_DIR . "/Web/StandardSessionStorageFactory/tmp";

    public function setUp(): void
    {
        TestHelper::cleanDirectory(self::TMP_DIR);
    }

    /**
     * @return string
     */
    private function getDefaultPath(): string
    {
        $savePath = session_save_path();
        return strlen($savePath) ? $savePath : sys_get_temp_dir();
    }

    /**
     * @return float
     */
    private function getDefaultGcProbability(): float
    {
        $p = ini_get("session.gc_probability");
        $d = ini_get("session.gc_divisor");
        return (0 < $p && 0 < $d) ? (float) ($p / $d) : 0.0;
    }

    /**
     * @return Logger
     */
    private function getTestLogger(): Logger
    {
        $logdir = self::TMP_DIR . "/logs";
        is_dir($logdir) || mkdir($logdir, 0777, true);
        return (new LoggerBuilder())->setStorage(new FileLogStorage($logdir))->build();
    }

    /**
     * @param array $arr
     * @return SessionStorage
     */
    private function createStorageByArray(array $arr): SessionStorage
    {
        $obj  = new StandardSessionStorageFactory();
        $prop = new ArrayProperties(["session" => $arr]);
        $conf = new Config($prop);
        return $obj->create($conf, new FileDataStorage(self::TMP_DIR), $this->getTestLogger());
    }

    /**
     * @param array $arr
     * @param string $expected
     * @covers ::create
     * @covers ::getSessionSavePath
     * @dataProvider provideTestGetSessionSavePath
     */
    public function testGetSessionSavePath(array $arr, string $expected): void
    {
        $ss = $this->createStorageByArray($arr);
        $c1 = $ss->getSessionContainer();
        $c2 = new FileSessionContainer($expected, $this->getTestLogger());
        $this->assertEquals($c2, $c1);
    }

    /**
     * @return array
     */
    public function provideTestGetSessionSavePath(): array
    {
        $tmp  = self::TMP_DIR;
        $arr1 = [];
        $arr2 = ["dirname" => "test01"];
        $arr3 = ["dirname" => [1, 2, 3]];
        return [
            [$arr1, "{$tmp}/sessions"],
            [$arr2, "{$tmp}/test01"],
            [$arr3, "{$tmp}/sessions"],
        ];
    }

    /**
     * @covers ::create
     * @covers ::getSessionSavePath
     */
    public function testGetSessionSavePathWithoutData(): void
    {
        $obj  = new StandardSessionStorageFactory();
        $prop = new ArrayProperties(["session" => ["dirname" => "test02"]]);
        $conf = new Config($prop);
        $ss   = $obj->create($conf);

        $c1 = $ss->getSessionContainer();
        $c2 = new FileSessionContainer($this->getDefaultPath());
        $this->assertEquals($c2, $c1);
    }

    /**
     * @param array $arr
     * @param string $expected
     * @covers ::create
     * @covers ::getSessionKey
     * @dataProvider provideTestGetSessionKey
     */
    public function testGetSessionKey(array $arr, string $expected): void
    {
        $ss = $this->createStorageByArray($arr);
        $this->assertSame($expected, $ss->getKey());
    }

    /**
     * @return array
     */
    public function provideTestGetSessionKey(): array
    {
        $def  = session_name();
        $arr1 = [];
        $arr2 = ["keyname" => "test_sess_id"];
        $arr3 = ["keyname" => ["a" => 1]];
        return [
            [$arr1, $def],
            [$arr2, "test_sess_id"],
            [$arr3, $def],
        ];
    }

    /**
     * @param array $arr
     * @param int $expected
     * @covers ::create
     * @covers ::getMaxAge
     * @dataProvider provideTestGetMaxAge
     */
    public function testGetMaxAge(array $arr, int $expected): void
    {
        $ss = $this->createStorageByArray($arr);
        $this->assertSame($expected, $ss->getMaxAge());
    }

    /**
     * @return array
     */
    public function provideTestGetMaxAge(): array
    {
        $def  = (int) ini_get("session.gc_maxlifetime");
        $arr1 = [];
        $arr2 = ["max-age" => 1800];
        $arr3 = ["max-age" => "asdf"];
        $arr4 = ["max-age" => 30];
        $arr5 = ["max-age" => 9000];
        return [
            [$arr1, $def],
            [$arr2, 1800],
            [$arr3, $def],
            [$arr4, 60],
            [$arr5, 7200],
        ];
    }

    /**
     * @param array $arr
     * @param float $expected
     * @covers ::create
     * @covers ::getGcProbability
     * @dataProvider provideTestGetGcProbability
     */
    public function testGetGcProbability(array $arr, float $expected): void
    {
        $ss = $this->createStorageByArray($arr);
        $this->assertSame($expected, $ss->getGcProbability());
    }

    /**
     * @return array
     */
    public function provideTestGetGcProbability(): array
    {
        $def  = $this->getDefaultGcProbability();
        $arr1 = [];
        $arr2 = ["gc-probability" => 0.125];
        $arr3 = ["gc-probability" => "asdf"];
        $arr4 = ["gc-probability" => -0.25];
        $arr5 = ["gc-probability" => 1.0675];
        return [
            [$arr1, $def],
            [$arr2, 0.125],
            [$arr3, $def],
            [$arr4, 0.0],
            [$arr5, 1.0],
        ];
    }

    /**
     * @covers ::create
     */
    public function testCreate(): void
    {
        $testdir = self::TMP_DIR . "/test/dir1";
        mkdir($testdir, 0777, true);

        $expected = (new SessionStorageBuilder())
            ->setSessionContainer(new FileSessionContainer($testdir, $this->getTestLogger()))
            ->setKey("testkey")
            ->setMaxAge(900)
            ->setGcProbability(0.125)
            ->build();

        $arr = [
            "dirname"        => "/test/dir1",
            "keyname"        => "testkey",
            "max-age"        => 900,
            "gc-probability" => 0.125,
        ];
        $this->assertEquals($expected, $this->createStorageByArray($arr));
    }
}
