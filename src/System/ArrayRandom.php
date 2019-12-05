<?php

namespace Woof\System;

use InvalidArgumentException;
use LogicException;

/**
 * 乱数列を直接指定する Random の実装です。
 * コンストラクタ引数に指定した整数配列の各要素を、次の乱数として順番に出力します。
 * すべての要素を出力した後は再びはじめの要素に戻ります。
 *
 * このクラスは、乱数に依存した処理のエッジケースのテストで使用されることを想定しています。
 */
class ArrayRandom implements Random
{
    /**
     * @var int[]
     */
    private $seq;

    /**
     * @var int
     */
    private $index;

    /**
     * 指定された整数配列を乱数列として使用する ArrayRandom オブジェクトを生成します。
     *
     * @param int[] $seq
     */
    public function __construct(array $seq)
    {
        if (!count($seq)) {
            throw new InvalidArgumentException("Empty sequence specified");
        }

        $this->seq   = array_values($seq);
        $this->index = 0;
    }

    /**
     * 次の乱数を取得します。
     *
     * @return int
     */
    public function next(): int
    {
        $index = $this->index;
        $next  = (int) $this->seq[$index];
        if ($next < 0 || mt_getrandmax() < $next) {
            throw new LogicException("Invalid random number: {$next}");
        }
        $this->index = ($index + 1) % count($this->seq);
        return $next;
    }
}
