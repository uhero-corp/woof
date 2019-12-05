<?php

namespace Woof\Http\Response;

/**
 * レスポンスボディを持たない HTTP レスポンスがダミーとして保持するための Body の実装です。
 */
class EmptyBody implements Body
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
     * @return EmptyBody
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

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return "";
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return "";
    }

    /**
     * @return bool
     */
    public function sendOutput(): bool
    {
        return true;
    }
}
