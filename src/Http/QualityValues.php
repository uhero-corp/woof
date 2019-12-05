<?php

namespace Woof\Http;

use InvalidArgumentException;

/**
 * 複数の項目の優先順位を指定するためのヘッダーフィールドです。
 * Accept-Language, Accept-Encoding などが該当します。
 */
class QualityValues implements HeaderField
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $qvalueList;

    /**
     * @param string $name
     * @param array $qvalueList
     */
    public function __construct(string $name, array $qvalueList)
    {
        if (!count($qvalueList)) {
            throw new InvalidArgumentException("Empty array specified");
        }
        foreach ($qvalueList as $key => $value) {
            $this->validateQvalue($key, $value);
            $qvalueList[$key] = $this->fixQvalue($value);
        }
        arsort($qvalueList);
        $this->name       = $name;
        $this->qvalueList = $qvalueList;
    }

    /**
     * それぞれの qvalue の値が 0 以上 1 以下の小数となっていることを確認します.
     *
     * @param string $key
     * @param string $value
     * @throws InvalidArgumentException
     */
    private function validateQvalue(string $key, $value): void
    {
        if (!preg_match("/\\A[a-zA-Z0-9_\\-\\/\\+\\*]+\\z/", $key)) {
            throw new InvalidArgumentException("Invalid key: {$key}");
        }
        if (!$this->checkValue($value)) {
            throw new InvalidArgumentException("Invalid value: {$value}");
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    private function checkValue($value)
    {
        if (is_float($value) && 0 <= $value && $value <= 1.0) {
            return true;
        }
        $str = (string) $value;
        if ($str === "0" || $str === "1") {
            return true;
        }
        if (preg_match("/\\A1\\.0{1,3}\\z/", $str)) {
            return true;
        }
        if (preg_match("/\\A0?\\.[0-9]{1,3}\\z/", $str)) {
            return true;
        }

        return false;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function fixQvalue($value): string
    {
        $rounded = is_float($value) ? round($value, 3) : $value;
        return (string) $rounded;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $callback = function ($key, $value) {
            $v = (float) $value;
            return $v === 1.0 ? $key : "{$key};q={$value}";
        };
        $qvalueList = $this->qvalueList;
        return implode(",", array_map($callback, array_keys($qvalueList), array_values($qvalueList)));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->qvalueList;
    }
}
