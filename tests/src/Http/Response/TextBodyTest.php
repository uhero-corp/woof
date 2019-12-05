<?php

namespace Woof\Http\Response;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\TextBody
 */
class TextBodyTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_STRING = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.";

    /**
     * @return TextBody
     */
    private function getTestObject(): TextBody
    {
        return new TextBody(self::TEST_STRING, "text/plain");
    }

    /**
     * @covers ::__construct
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame(self::TEST_STRING, $obj->getOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::sendOutput
     */
    public function testSendOutput(): void
    {
        $this->expectOutputString(self::TEST_STRING);
        $obj = $this->getTestObject();
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
        $this->assertSame(123, $obj->getContentLength());
    }
}
