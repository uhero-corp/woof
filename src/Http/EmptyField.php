<?php

namespace Woof\Http;

/**
 * 指定されたヘッダーが存在しないことをあらわす HeaderField です。
 */
class EmptyField implements HeaderField
{
    /**
     * このクラスは getInstance() で初期化します。
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {

    }

    /**
     * 常に空文字列を返します。
     *
     * @return string
     */
    public function format(): string
    {
        return "";
    }

    /**
     * 常に空文字列を返します。
     *
     * @return string
     */
    public function getName(): string
    {
        return "";
    }

    /**
     * 常に NULL を返します。
     *
     * @return mixed
     */
    public function getValue()
    {
        return null;
    }

    /**
     * 唯一の EmptyField インスタンスを返します。
     *
     * @return EmptyField
     */
    public static function getInstance(): self
    {
        // @codeCoverageIgnoreStart
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        // @codeCoverageIgnoreEnd
        return $instance;
    }
}
