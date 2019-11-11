<?php

namespace Woof\System;

/**
 * システム時刻を出力する Clock です。
 *
 * @codeCoverageIgnore
 */
class DefaultClock implements Clock
{
    /**
     * このクラスは getInstance() で初期化します。
     */
    private function __construct()
    {

    }

    /**
     * 唯一の DefaultClock インスタンスを返します。
     *
     * @return DefaultClock
     */
    public static function getInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * この実装は time() の結果を返します。
     *
     * @return int
     */
    public function getTime(): int
    {
        return time();
    }
}
