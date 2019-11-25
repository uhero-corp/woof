<?php

namespace Woof\System;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use TestHelper;

/**
 * @coversDefaultClass Woof\System\FileHandler
 */
class FileHandlerTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpdir;

    protected function setUp(): void
    {
        $datadir = TEST_DATA_DIR . "/System/FileHandler";
        $tmpdir  = "{$datadir}/tmp";
        TestHelper::cleanDirectory($tmpdir);
        TestHelper::copyDirectory("{$datadir}/subjects", $tmpdir);

        $this->tmpdir = $tmpdir;
    }

    /**
     * @covers ::__construct
     */
    public function testConstructFailByEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FileHandler("");
    }

    /**
     * @covers ::__construct
     */
    public function testConstructFailByNonExistingName(): void
    {
        $this->expectException(FileSystemException::class);
        new FileHandler(TEST_DATA_DIR . "/notfound");
    }

    /**
     * @covers ::__construct
     * @covers ::formatFullPath
     */
    public function testFormatFullPath(): void
    {
        $tmpdir = $this->tmpdir;
        $obj    = new FileHandler($tmpdir);
        $this->assertSame("{$tmpdir}/hoge/index.html", $obj->formatFullpath("hoge/index.html"));
    }

    /**
     * @param string $path
     * @param string $expected
     * @covers ::__construct
     * @covers ::formatFullPath
     * @covers ::cleanPath
     * @dataProvider provideTestCleanPath
     */
    public function testCleanPath(string $path, string $expected): void
    {
        $tmpdir = $this->tmpdir;
        $obj    = new FileHandler($tmpdir);
        $this->assertSame("{$tmpdir}/{$expected}", $obj->formatFullpath($path));
    }

    /**
     * @return array
     */
    public function provideTestCleanPath(): array
    {
        return [
            ["//foo/bar///buz//", "foo/bar/buz"],
            ["/./foo/bar/./buz.html", "foo/bar/buz.html"],
            ["../foo/bar/../buz", "foo/buz"],
        ];
    }

    /**
     * @param string $path
     * @covers ::__construct
     * @covers ::formatFullPath
     * @dataProvider provideTestFormatFullPathFail
     */
    public function testFormatFullPathFail(string $path): void
    {
        $this->expectException(InvalidArgumentException::class);
        $obj = new FileHandler($this->tmpdir);
        $obj->formatFullPath($path);
    }

    /**
     * @return array
     */
    public function provideTestFormatFullPathFail(): array
    {
        return [
            [""],
            ["/./..///.././"],
        ];
    }
    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet(): void
    {
        $tmpdir   = $this->tmpdir;
        $obj      = new FileHandler($tmpdir);
        $expected = file_get_contents("{$tmpdir}/test01/sample.txt");
        $this->assertSame($expected, $obj->get("test01/sample.txt"));
        $this->assertSame("", $obj->get("test02/notfound.txt"));
    }

    /**
     * @covers ::__construct
     * @covers ::contains
     */
    public function testContains(): void
    {
        $obj = new FileHandler($this->tmpdir);
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
        $obj      = new FileHandler($tmpdir);
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
        $obj    = new FileHandler($tmpdir);
        $obj->append("test02/test.log", "first line" . PHP_EOL);
        $obj->append("test02/test.log", "second line" . PHP_EOL);
        $obj->append("test02/test.log", "third line" . PHP_EOL);

        $expected = "first line" . PHP_EOL . "second line" . PHP_EOL . "third line" . PHP_EOL;
        $this->assertSame($expected, file_get_contents("{$tmpdir}/test02/test.log"));
    }
}
