<?php

namespace Woof;

/**
 * 2 種類の Resources オブジェクトを透過的に取り扱うクラスです。
 * このオブジェクトに対するメソッド呼び出しは下記のように処理されます。
 *
 * - 指定されたキーがプライマリの Resources オブジェクトに存在する場合: プライマリのオブジェクトが結果を返します
 * - 指定されたキーがプライマリに存在しない場合: セカンダリのオブジェクトが結果を返します
 * - 指定されたキーがプライマリとセカンダリのどちらにも存在しない場合: ResourceNotFoundException をスローします
 */
class FallbackResources implements Resources
{
    /**
     * @var Resources
     */
    private $primary;

    /**
     * @var Resources
     */
    private $secondary;

    /**
     * @param Resources $primary
     * @param Resources $secondary
     */
    public function __construct(Resources $primary, Resources $secondary)
    {
        $this->primary   = $primary;
        $this->secondary = $secondary;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool
    {
        return $this->primary->contains($key) || $this->secondary->contains($key);
    }

    /**
     * @param string $key
     * @return string
     * @throws ResourceNotFoundException
     */
    public function get(string $key): string
    {
        if ($this->primary->contains($key)) {
            return $this->primary->get($key);
        }
        if ($this->secondary->contains($key)) {
            return $this->secondary->get($key);
        }

        throw new ResourceNotFoundException("Resource not found: '{$key}'");
    }
}
