<?php

namespace Woof\System;

/**
 * 現在時刻を整数値で出力するインタフェースです。
 */
interface Clock
{
    /**
     *
     * @return int
     */
    public function getTime(): int;
}
