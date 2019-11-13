<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\ContentDisposition
 */
class ContentDispositionTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $obj = new ContentDisposition("sample.png");
        $this->assertSame("Content-Disposition", $obj->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $obj1 = new ContentDisposition();
        $this->assertSame("", $obj1->getValue());
        $obj2 = new ContentDisposition("test image.jpg");
        $this->assertSame("test image.jpg", $obj2->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::format
     */
    public function testFormat(): void
    {
        $obj1 = new ContentDisposition();
        $this->assertSame("attachment", $obj1->format());
        $obj2 = new ContentDisposition("sample image.jpg");
        $this->assertSame("attachment; filename=\"sample%20image.jpg\"", $obj2->format());
    }
}
