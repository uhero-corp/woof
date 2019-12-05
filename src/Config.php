<?php

namespace Woof;

use Woof\Util\ArrayProperties;
use Woof\Util\Properties;

class Config
{
    /**
     * @var Properties
     */
    private $properties;

    /**
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function checkScalar($value): bool
    {
        return is_scalar($value) || ($value === null);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function checkNumber($value): bool
    {
        return is_numeric($value) || is_bool($value) || ($value === null);
    }

    /**
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     * @return mixed
     */
    private function getMinMax($value, $min = null, $max = null)
    {
        if ($min !== null && $value < $min) {
            return $min;
        }
        if ($max !== null && $max < $value) {
            return $max;
        }
        return $value;
    }

    /**
     * @param string $name
     * @param int $defaultValue
     * @return int
     */
    public function getInt(string $name, int $defaultValue = 0, int $min = null, int $max = null): int
    {
        $result = $this->properties->get($name, $defaultValue);
        $value  = $this->checkNumber($result) ? (int) $result : $defaultValue;
        return $this->getMinMax($value, $min, $max);
    }

    /**
     * @param string $name
     * @param float $defaultValue
     * @return float
     */
    public function getFloat(string $name, float $defaultValue = 0.0, float $min = null, float $max = null): float
    {
        $result = $this->properties->get($name, $defaultValue);
        $value  = $this->checkNumber($result) ? (float) $result : $defaultValue;
        return $this->getMinMax($value, $min, $max);
    }

    /**
     * @param string $name
     * @param string $defaultValue
     * @return string
     */
    public function getString(string $name, string $defaultValue = ""): string
    {
        $result = $this->properties->get($name, $defaultValue);
        return $this->checkScalar($result) ? $this->scalarToString($result) : $defaultValue;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function scalarToString($value): string
    {
        if ($value === true) {
            return "true";
        }
        if ($value === false) {
            return "false";
        }
        if ($value === null) {
            return "null";
        }
        return (string) $value;
    }

    /**
     * @param string $name
     * @param array $defaultValue
     * @return array
     */
    public function getArray(string $name, array $defaultValue = []): array
    {
        $result = $this->properties->get($name, $defaultValue);
        return is_array($result) ? $result : $defaultValue;
    }

    /**
     * 指定された名前の設定値を Config オブジェクトとして取得します。
     * 基本的な挙動は getArray() と同じで、返り値が Config 型にキャストされることのみ異なります。
     *
     * @param string $name
     * @param array $defaultValue
     * @return Config
     */
    public function getSubConfig(string $name, array $defaultValue = []): Config
    {
        return new Config(new ArrayProperties($this->getArray($name, $defaultValue)));
    }

    /**
     * @param string $name
     * @param bool $defaultValue
     * @return bool
     */
    public function getBool(string $name, bool $defaultValue = false): bool
    {
        $result = $this->properties->get($name, $defaultValue);
        if (is_bool($result)) {
            return $result;
        }
        if (is_string($result)) {
            return $this->stringToBool($result, $defaultValue);
        }
        return $defaultValue;
    }

    /**
     * @param string $value
     * @param bool $defaultValue
     * @return bool
     */
    private function stringToBool(string $value, bool $defaultValue): bool
    {
        $str   = strtolower($value);
        $words = [
            "true"  => true,
            "false" => false,
            "yes"   => true,
            "no"    => false,
            "on"    => true,
            "off"   => false,
        ];
        return $words[$str] ?? $defaultValue;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function contains(string $name): bool
    {
        return $this->properties->contains($name);
    }
}
