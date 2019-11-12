<?php

namespace Woof\Log;

interface LogStorage
{
    /**
     * 指定されたメッセージ・時刻・ログレベルでログを記録します。
     * 第 1 引数の $content には LogFormat オブジェクトで書式化された結果の文字列が指定されます。
     * 成功時に true を返します。
     *
     * @param string $content
     * @param int $time
     * @param int $level
     * @return bool
     */
    public function write(string $content, int $time, int $level): bool;
}
