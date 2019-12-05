<?php

namespace Woof\Http;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Http\EmptyField
 */
class EmptyFieldTest extends TestCase
{
    /**
     * @covers ::getName
     */
    public function testGetName()
    {
        $obj = EmptyField::getInstance();
        $this->assertSame("", $obj->getName());
    }

    /**
     * @covers ::getValue
     */
    public function testGetValue()
    {
        $obj = EmptyField::getInstance();
        $this->assertNull($obj->getValue());
    }

    /**
     * @covers ::format
     */
    public function testFormat()
    {
        $obj = EmptyField::getInstance();
        $this->assertSame("", $obj->format());
    }

    /**
     * @covers ::getInstance
     */
    public function testGetInstance()
    {
        $obj1 = EmptyField::getInstance();
        $obj2 = EmptyField::getInstance();
        $this->assertInstanceOf(EmptyField::class, $obj1);
        $this->assertSame($obj1, $obj2);
    }
}
