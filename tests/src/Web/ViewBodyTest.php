<?php

namespace Woof\Web;

use PHPUnit\Framework\TestCase;
use Woof\FileResources;
use Woof\Resources;

/**
 * @coversDefaultClass Woof\Web\ViewBody
 */
class ViewBodyTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_DIR = TEST_DATA_DIR . "/Web/ViewBody/subjects";

    /**
     * @return ViewBody
     */
    private function getTestObject(): ViewBody
    {
        return new ViewBody(new ViewBodyTest_TestView(), new FileResources(self::TEST_DIR), new Context("/base"));
    }

    /**
     * @return string
     */
    private function getTestData(): string
    {
        return file_get_contents(self::TEST_DIR . "/sample.txt");
    }
    /**
     * @covers ::__construct
     * @covers ::getView
     */
    public function testGetView(): void
    {
        $this->assertEquals(new ViewBodyTest_TestView(), $this->getTestObject()->getView());
    }

    /**
     * @covers ::__construct
     * @covers ::getContentLength
     */
    public function testGetContentLength(): void
    {
        $this->assertSame(strlen($this->getTestData()), $this->getTestObject()->getContentLength());
    }

    /**
     * @covers ::__construct
     * @covers ::getContentType
     */
    public function testGetContentType(): void
    {
        $this->assertSame("text/plain", $this->getTestObject()->getContentType());
    }

    /**
     * @covers ::__construct
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $this->assertSame($this->getTestData(), $this->getTestObject()->getOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::sendOutput
     */
    public function testSendOutput(): void
    {
        $this->expectOutputString($this->getTestData());
        $this->assertTrue($this->getTestObject()->sendOutput());
    }
}

class ViewBodyTest_TestView implements View
{
    /**
     * @return string
     */
    public function getContentType(): string
    {
        return "text/plain";
    }

    /**
     * @param Resources $resources
     * @param Context $context
     * @return string
     */
    public function render(Resources $resources, Context $context): string
    {
        return $resources->get("sample.txt");
    }
}
