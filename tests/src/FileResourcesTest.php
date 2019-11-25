<?php

namespace Woof;

use PHPUnit\Framework\TestCase;
use Woof\FileResources;

/**
 * @coversDefaultClass Woof\FileResources
 */
class FileResourcesTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_DIR = TEST_DATA_DIR . "/FileResources/subjects";

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet(): void
    {
        $tmpdir   = self::TEST_DIR;
        $obj      = new FileResources($tmpdir);
        $expected = file_get_contents("{$tmpdir}/test01/sample.txt");
        $this->assertSame($expected, $obj->get("test01/sample.txt"));
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetFail(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $obj = new FileResources(self::TEST_DIR);
        $obj->get("test02/notfound.txt");
    }

    /**
     * @covers ::__construct
     * @covers ::contains
     */
    public function testContains(): void
    {
        $obj = new FileResources(self::TEST_DIR);
        $this->assertTrue($obj->contains("test01/sample.txt"));
        $this->assertFalse($obj->contains("test03/aaaa.txt"));
    }

    /**
     * @covers ::__construct
     * @covers ::formatPath
     */
    public function testFormatPath(): void
    {
        $tmpdir = self::TEST_DIR;
        $obj    = new FileResources($tmpdir);
        $this->assertSame("{$tmpdir}/test03/xxxx.html", $obj->formatPath("test03/xxxx.html"));
    }
}
