<?php

namespace Woof\System;

/**
 * 常に固定の値を出力する Clock の実装です。
 * このクラスはテストにおける使用を想定しています。
 */
class FixedClock implements Clock
{
    /**
     * @var int
     */
    private $time;

    /**
     * 指定された Unix time を現在時刻とする FixedClock オブジェクトを生成します。
     *
     * @param int $time
     */
    public function __construct(int $time)
    {
        $this->time = (int) $time;
    }

    /**
     * 初期化時に指定された Unix time の値を返します。
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }
}
