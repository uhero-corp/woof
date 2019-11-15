<?php

namespace Woof\Util;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\Util\RawDataObject
 */
class RawDataObjectTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::toValue
     */
    public function testToValue(): void
    {
        $arr = [
            "abc" => 123,
            "xyz" => "test",
        ];
        $obj = new RawDataObject($arr);
        $this->assertSame($arr, $obj->toValue());
    }

}
