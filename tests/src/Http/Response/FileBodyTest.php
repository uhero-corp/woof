<?php

namespace Woof\Http\Response;

use PHPUnit\Framework\TestCase;
use Woof\System\FileSystemException;

/**
 * @coversDefaultClass Woof\Http\Response\FileBody
 */
class FileBodyTest extends TestCase
{
    /**
     * @var string
     */
    const DATA_DIR = TEST_DATA_DIR . "/Http/Response/FileBody";

    /**
     * @return FileBody
     */
    private function getTestObject(): FileBody
    {
        return new FileBody(self::DATA_DIR . "/sample.txt", "text/plain");
    }

    /**
     * @covers ::__construct
     */
    public function testConstructFailByFileNotFound(): void
    {
        $this->expectException(FileSystemException::class);
        new FileBody(self::DATA_DIR . "/notfound.txt", "text/plain");
    }

    /**
     * @covers ::__construct
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $obj      = $this->getTestObject();
        $expected = file_get_contents(self::DATA_DIR . "/sample.txt");
        $this->assertSame($expected, $obj->getOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::sendOutput
     */
    public function testSendOutput(): void
    {
        $obj      = $this->getTestObject();
        $expected = file_get_contents(self::DATA_DIR . "/sample.txt");
        $this->expectOutputString($expected);
        $this->assertTrue($obj->sendOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::getContentType
     */
    public function testGetContentType(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame("text/plain", $obj->getContentType());
    }

    /**
     * @covers ::__construct
     * @covers ::getContentLength
     */
    public function testGetContentLength(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame(446, $obj->getContentLength());
    }
}
