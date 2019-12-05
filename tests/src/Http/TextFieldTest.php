<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\TextField
 */
class TextFieldTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::format
     */
    public function testFormat(): void
    {
        $obj = new TextField("Pragma", "no-cache");
        $this->assertSame("no-cache", $obj->format());
    }

    /**
     * @covers ::__construct
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $obj = new TextField("Pragma", "no-cache");
        $this->assertSame("Pragma", $obj->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $obj = new TextField("Pragma", "no-cache");
        $this->assertSame("no-cache", $obj->getValue());
    }
}
