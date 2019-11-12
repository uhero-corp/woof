<?php

namespace Woof\Log;

/**
 * アプリケーションログの書式をカスタマイズするためのインタフェースです。
 */
interface LogFormat
{
    /**
     * 指定されたメッセージ・発生時刻・ログレベルによるログ出力を書式化します。
     *
     * @param string $message
     * @param int $time
     * @param int $level
     * @return string
     */
    public function format(string $message, int $time, int $level): string;
}
