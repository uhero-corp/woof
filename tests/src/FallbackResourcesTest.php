<?php

namespace Woof;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\FallbackResources
 */
class FallbackResourcesTest extends TestCase
{
    /**
     * @var string
     */
    const TEST_DIR = TEST_DATA_DIR . "/FallbackResources/subjects";

    /**
     * @return FallbackResources
     */
    private function createTestObject(): FallbackResources
    {
        $tmpdir = self::TEST_DIR;
        $pri    = new FileResources("{$tmpdir}/test01");
        $sec    = new FileResources("{$tmpdir}/test02");
        return new FallbackResources($pri, $sec);
    }

    /**
     * @param string $key
     * @param string $expected
     * @dataProvider provideTestGet
     * @covers ::__construct
     * @covers ::get
     */
    public function testGet(string $key, string $expected): void
    {
        $this->assertSame($expected, trim($this->createTestObject()->get($key)));
    }

    /**
     * @return array
     */
    public function provideTestGet(): array
    {
        return [
            ["apple.txt", "THIS IS AN APPLE"],
            ["banana.txt", "This is a banana"],
            ["cherry.txt", "THIS IS A CHERRY"],
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testGetFail(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->createTestObject()->get("grape.txt");
    }

    /**
     * @param string $key
     * @param bool $expected
     * @dataProvider provideTestContains
     */
    public function testContains(string $key, bool $expected): void
    {
        $this->assertSame($expected, $this->createTestObject()->contains($key));
    }

    /**
     * @return array
     */
    public function provideTestContains(): array
    {
        return [
            ["apple.txt", true],
            ["banana.txt", true],
            ["cherry.txt", true],
            ["pineapple.txt", false],
        ];
    }
}
