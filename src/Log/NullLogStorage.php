<?php

namespace Woof\Log;

/**
 * 書き込みを一切行わない LogStorage です。
 */
class NullLogStorage implements LogStorage
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
     * このクラスの唯一のインスタンスを取得します。
     *
     * @return NullLogStorage
     */
    public static function getInstance()
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
     * 何もせずに常に true を返します。
     *
     * @param string $content
     * @param int $time
     * @param int $level
     * @return bool
     */
    public function write(string $content, int $time, int $level): bool
    {
        // noop
        return true;
    }
}
