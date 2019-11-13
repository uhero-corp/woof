<?php

namespace Woof\Http;

/**
 * 指定された文字列をヘッダー値とする、シンプルなヘッダーフィールドです。
 */
class TextField implements HeaderField
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
