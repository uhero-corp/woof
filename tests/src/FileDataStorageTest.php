<?php

namespace Woof;

use PHPUnit\Framework\TestCase;
use TestHelper;

/**
 * @coversDefaultClass Woof\FileDataStorage
 */
class FileDataStorageTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpdir;

    protected function setUp(): void
    {
        $datadir = TEST_DATA_DIR . "/FileDataStorage";
        $tmpdir  = "{$datadir}/tmp";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory("{$datadir}/subjects", $tmpdir);

        $this->tmpdir = $tmpdir;
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet(): void
    {
        $tmpdir   = $this->tmpdir;
        $obj      = new FileDataStorage($tmpdir);
        $expected = file_get_contents("{$tmpdir}/test01/sample.txt");
        $this->assertSame($expected, $obj->get("test01/sample.txt"));
        $this->assertSame($expected, $obj->get("test01/sample.txt", "alternative"));
        $this->assertSame("alternative", $obj->get("test01/notfound.txt", "alternative"));
    }

    /**
     * @covers ::__construct
     * @covers ::contains
     */
    public function testContains(): void
    {
        $obj = new FileDataStorage($this->tmpdir);
        $this->assertFalse($obj->contains("test01/aaaa.txt"));
        $this->assertTrue($obj->contains("test01/sample.txt"));
    }

    /**
     * @covers ::__construct
     * @covers ::put
     * @covers ::<private>
     */
    public function testPut(): void
    {
        $tmpdir   = $this->tmpdir;
        $testfile = "{$tmpdir}/test02/newfile.txt";
        $obj      = new FileDataStorage($tmpdir);
        $this->assertFileNotExists($testfile);
        $obj->put("test02/newfile.txt", "This is test");
        $this->assertFileExists($testfile);
        $this->assertSame("This is test", file_get_contents($testfile));
    }

    /**
     * @covers ::__construct
     * @covers ::append
     */
    public function testAppend(): void
    {
        $tmpdir = $this->tmpdir;
        $obj    = new FileDataStorage($tmpdir);
        $obj->append("test02/test.log", "first line" . PHP_EOL);
        $obj->append("test02/test.log", "second line" . PHP_EOL);
        $obj->append("test02/test.log", "third line" . PHP_EOL);

        $expected = "first line" . PHP_EOL . "second line" . PHP_EOL . "third line" . PHP_EOL;
        $this->assertSame($expected, file_get_contents("{$tmpdir}/test02/test.log"));
    }

    /**
     * @covers ::__construct
     * @covers ::formatPath
     */
    public function testFormatPath(): void
    {
        $tmpdir = $this->tmpdir;
        $obj    = new FileDataStorage($tmpdir);
        $this->assertSame("{$tmpdir}/hoge/index.html", $obj->formatPath("hoge/index.html"));
    }
}
