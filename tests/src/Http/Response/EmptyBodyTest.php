<?php

namespace Woof\Http\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\EmptyBody
 */
class EmptyBodyTest extends TestCase
{
    /**
     * @covers ::getContentLength
     */
    public function testGetContentLength(): void
    {
        $obj = EmptyBody::getInstance();
        $this->assertSame(0, $obj->getContentLength());
    }

    /**
     * @covers ::getContentType
     */
    public function testGetContentType(): void
    {
        $obj = EmptyBody::getInstance();
        $this->assertSame("", $obj->getContentType());
    }

    /**
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $obj = EmptyBody::getInstance();
        $this->assertSame("", $obj->getOutput());
    }

    /**
     * @covers ::sendOutput
     */
    public function testSendOutput(): void
    {
        $obj = EmptyBody::getInstance();
        $this->expectOutputString("");
        $this->assertTrue($obj->sendOutput());
    }

    /**
     * @covers ::getInstance
     */
    public function testGetInstance(): void
    {
        $obj1 = EmptyBody::getInstance();
        $obj2 = EmptyBody::getInstance();
        $this->assertInstanceOf(EmptyBody::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }
}
