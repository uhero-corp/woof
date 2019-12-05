<?php

namespace Woof\System;

interface Random
{
    /**
     * 乱数の結果として 0 以上 mt_getrandmax() 以下の整数を返します。
     *
     * @return int
     */
    public function next(): int;
}
