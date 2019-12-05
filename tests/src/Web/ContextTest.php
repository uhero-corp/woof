<?php

namespace Woof\Web;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Web\Context
 */
class ContextTest extends TestCase
{
    /**
     * @param string $path
     * @param string $expected
     * @covers ::__construct
     * @covers ::getRootPath
     * @dataProvider provideTestGetRootPath
     */
    public function testGetRootPath(string $path, string $expected): void
    {
        $obj = new Context($path);
        $this->assertSame($expected, $obj->getRootPath());
    }

    /**
     * @return array
     */
    public function provideTestGetRootPath(): array
    {
        return [
            ["", "/"],
            ["/", "/"],
            ["hoge/fuga", "/hoge/fuga"],
            ["/hoge/fuga/", "/hoge/fuga"],
        ];
    }

    /**
     * @param string $path
     * @param string $expected
     * @covers ::__construct
     * @covers ::formatHref
     * @dataProvider provideTestFormatPathWithoutQuery
     */
    public function testFormatPathWithoutQuery(string $path, string $expected): void
    {
        $obj = new Context("/base");
        $this->assertSame($expected, $obj->formatHref($path));
    }

    /**
     * @return array
     */
    public function provideTestFormatPathWithoutQuery(): array
    {
        return [
            ["", "/base/"],
            ["/", "/base/"],
            ["asdf/", "/base/asdf/"],
            ["/asdf/index.html", "/base/asdf/index.html"],
            ["https://www.example.com/xxxx/a.html", "https://www.example.com/xxxx/a.html"],
            ["//www.example.com/xxxx/a.html", "//www.example.com/xxxx/a.html"],
        ];
    }

    /**
     * @param array $query
     * @param string $expected
     * @covers ::__construct
     * @covers ::formatHref
     * @covers ::<private>
     * @dataProvider provideTestFormatPathWithQuery
     */
    public function testFormatPathWithQuery(array $query, string $expected): void
    {
        $obj = new Context("/base");
        $this->assertSame($expected, $obj->formatHref("search", $query));
    }

    /**
     * @return array
     */
    public function provideTestFormatPathWithQuery(): array
    {
        return [
            [[], "/base/search"],
            [["q" => "test"], "/base/search?q=test"],
            [["q" => "foo bar"], "/base/search?q=foo%20bar"],
            [["q" => "test", "category" => 1], "/base/search?q=test&category=1"],
            [["q" => "test", "cat" => [1, 2, 3]], "/base/search?q=test&cat%5B0%5D=1&cat%5B1%5D=2&cat%5B2%5D=3"],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::formatHref
     * @covers ::<private>
     */
    public function testFormatPathByCustomSeparator(): void
    {
        $obj = new Context("/base", ";");
        $this->assertSame("/base/search?q=test;cat=1", $obj->formatHref("search", ["q" => "test", "cat" => 1]));
    }

    /**
     * @covers ::__construct
     * @covers ::formatHref
     * @covers ::<private>
     */
    public function testFormatPathWithRawQuery(): void
    {
        $obj = new Context("/base");
        $this->assertSame("/base/inquiry?step=confirm", $obj->formatHref("/inquiry?step=confirm", ["var1" => "ignore", "var2" => "asdf"]));
    }
}
