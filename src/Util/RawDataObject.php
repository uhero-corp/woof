<?php

namespace Woof\Util;

/**
 * 何も変換を行わない、シンプルな DataObject の実装です。
 */
class RawDataObject implements DataObject
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function toValue()
    {
        return $this->value;
    }
}
