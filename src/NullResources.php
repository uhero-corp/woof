<?php

namespace Woof;

/**
 * リソースが存在しないことをあらわす Resources の実装です。
 */
class NullResources implements Resources
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
     * @return NullResources
     */
    public static function getInstance(): self
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
     * 常に false を返します。
     *
     * @param string $key
     * @return bool 常に false
     */
    public function contains(string $key): bool
    {
        return false;
    }

    /**
     * 常に ResourceNotFoundException をスローします。
     *
     * @param string $key
     * @return string
     * @throws ResourceNotFoundException
     */
    public function get(string $key): string
    {
        throw new ResourceNotFoundException("This instance cannot fetch any resources.");
    }
}
