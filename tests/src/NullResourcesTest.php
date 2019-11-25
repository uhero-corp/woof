<?php

namespace Woof;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\NullResources
 */
class NullResourcesTest extends TestCase
{
    /**
     * @covers ::getInstance
     */
    public function testGetInstance(): void
    {
        $obj1 = NullResources::getInstance();
        $obj2 = NullResources::getInstance();
        $this->assertInstanceOf(NullResources::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }

    /**
     * @covers ::contains
     */
    public function testContains(): void
    {
        $obj = NullResources::getInstance();
        $this->assertFalse($obj->contains("key"));
    }

    /**
     * @covers ::get
     */
    public function testGet(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        NullResources::getInstance()->get("key");
    }
}
