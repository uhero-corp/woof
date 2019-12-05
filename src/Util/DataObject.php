<?php

namespace Woof\Util;

/**
 * このオブジェクトが JSON, XML, CSV などのデータフォーマットに変換できることをあらわします。
 */
interface DataObject
{
    /**
     * その他のデータフォーマットに変換するための中間形式の値を返します。
     * 通常は配列や文字列を返り値とします。
     *
     * @return mixed
     */
    public function toValue();
}
