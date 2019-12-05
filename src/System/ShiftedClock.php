<?php

namespace Woof\System;

/**
 * 基準となる Clock から指定された秒数だけ進ませた (または遅らせた) 時刻を出力する Clock の実装です。
 * タイムゾーンに対応したシステムを構築する場合などに使用します。
 */
class ShiftedClock implements Clock
{
    /**
     * 基準の時刻から進ませる (または遅らせる) 秒数です。
     *
     * @var int
     */
    private $diff;

    /**
     * 基準となる時刻を出力する Clock です。
     *
     * @var Clock
     */
    private $original;

    /**
     * 進ませる (または遅らせる) 秒数およびその基準の時刻を提供する Clock を指定して、新しいインスタンスを作成します。
     * 第 2 引数を省略した場合は DefaultClock のインスタンスが適用されます。
     *
     * @param int $diff
     * @param Clock $original
     */
    public function __construct(int $diff, Clock $original = null)
    {
        $this->diff     = (int) $diff;
        $this->original = ($original instanceof Clock) ? $original : DefaultClock::getInstance();
    }

    /**
     * このオブジェクトの現在時刻を返します。
     *
     * @return int
     */
    public function getTime(): int
    {
        return $this->original->getTime() + $this->diff;
    }

    /**
     * 基準となる Clock オブジェクトを返します。
     *
     * @return Clock
     */
    public function getOriginal(): Clock
    {
        return $this->original;
    }
}
