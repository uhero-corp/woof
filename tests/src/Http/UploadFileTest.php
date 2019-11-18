<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\UploadFile
 */
class UploadFileTest extends TestCase
{
    /**
     * @var string
     */
    const DATA_DIR = TEST_DATA_DIR . "/Http/UploadFile";

    /**
     * @var UploadFile
     */
    private $object;

    protected function setUp(): void
    {
        $path         = self::DATA_DIR . "/tmp.txt";
        $size         = filesize($path);
        $this->object = new UploadFile("tmp.txt", $path, 0, $size);
    }

    /**
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $this->assertSame("tmp.txt", $this->object->getName());
    }

    /**
     * @covers ::getPath
     */
    public function testGetPath(): void
    {
        $expected = self::DATA_DIR . "/tmp.txt";
        $this->assertSame($expected, $this->object->getPath());
    }

    /**
     * @covers ::getErrorCode
     */
    public function testGetErrorCode(): void
    {
        $this->assertSame(0, $this->object->getErrorCode());
    }

    /**
     * @covers ::getSize
     */
    public function testGetSize(): void
    {
        $path     = self::DATA_DIR . "/tmp.txt";
        $expected = filesize($path);
        $this->assertSame($expected, $this->object->getSize());
    }

    /**
     * @covers ::getContents
     */
    public function testGetContents(): void
    {
        $path     = self::DATA_DIR . "/tmp.txt";
        $expected = file_get_contents($path);
        $this->assertSame($expected, $this->object->getContents());
    }

    /**
     * 添付ファイルが存在しない場合 getContents() は空文字列を返します。
     *
     * @covers ::__construct
     * @covers ::getContents
     */
    public function testGetContentsReturnsNullIfNotFound(): void
    {
        $obj = new UploadFile("notfound.txt", self::DATA_DIR . "/notfound.txt", UPLOAD_ERR_NO_FILE, 0);
        $this->assertSame("", $obj->getContents());
    }
}
