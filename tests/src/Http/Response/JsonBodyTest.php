<?php

namespace Woof\Http\Response;

use Woof\Util\DataObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\Response\JsonBody
 */
class JsonBodyTest extends TestCase
{
    /**
     * @return JsonBody
     */
    private function getTestObject(): JsonBody
    {
        $data    = [
            "str"    => "Hello / World",
            "list"   => [3, 5, 7, true, false],
            "object" => [
                "aaa" => "foo",
                "bbb" => "bar",
                "ccc" => "baz",
            ],
        ];
        $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        return new JsonBody($data, $options);
    }

    /**
     * @return string
     */
    private function getExpectedOutput(): string
    {
        return implode("\n", [
            '{',
            '    "str": "Hello / World",',
            '    "list": [',
            '        3,',
            '        5,',
            '        7,',
            '        true,',
            '        false',
            '    ],',
            '    "object": {',
            '        "aaa": "foo",',
            '        "bbb": "bar",',
            '        "ccc": "baz"',
            '    }',
            '}',
        ]);
    }

    /**
     * @covers ::__construct
     * @covers ::getData
     */
    public function testGetData(): void
    {
        $obj      = $this->getTestObject();
        $expected = [
            "str"    => "Hello / World",
            "list"   => [3, 5, 7, true, false],
            "object" => [
                "aaa" => "foo",
                "bbb" => "bar",
                "ccc" => "baz",
            ],
        ];
        $data = $obj->getData();
        $this->assertSame($expected, $data->toValue());
    }

    /**
     * @covers ::__construct
     * @covers ::getData
     */
    public function testGetDataByDataObject(): void
    {
        $data = new class implements DataObject {
            public function toValue()
            {
                return ["key" => "test"];
            }
        };
        $obj = new JsonBody($data);
        $this->assertSame($data, $obj->getData());
    }

    /**
     * @covers ::__construct
     * @covers ::getEncodeOptions
     */
    public function testGetEncodeOptions(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame(192, $obj->getEncodeOptions());
    }

    /**
     * @covers ::__construct
     * @covers ::getOutput
     */
    public function testGetOutput(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame($this->getExpectedOutput(), $obj->getOutput());
    }

    /**
     * @covers ::__construct
     * @covers ::sendOutput
     */
    public function testSendOutput(): void
    {
        $this->expectOutputString($this->getExpectedOutput());
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
        $this->assertSame("application/json", $obj->getContentType());
    }

    /**
     * @covers ::__construct
     * @covers ::getContentLength
     */
    public function testGetContentLength(): void
    {
        $obj = $this->getTestObject();
        $this->assertSame(200, $obj->getContentLength());
    }
}
