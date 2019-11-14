<?php

namespace Woof\Http;

/**
 * HTTP-date 形式の値を持つヘッダーフィールドです。
 */
class HttpDate implements HeaderField
{
    /**
     * @var HttpDateFormat
     */
    private $format;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $time;

    /**
     * 指定されたヘッダー名および時刻を持つ HttpDate オブジェクトを生成します。
     * 第 3 引数の HttpDateFormat は、デバッグやテストのために現在時刻を調整したい場合のみ指定してください。
     *
     * @param string $name ヘッダー名
     * @param int $time このシステムのタイムゾーンを基準とした Unix time
     * @param HttpDateFormat $format
     */
    public function __construct(string $name, int $time, HttpDateFormat $format = null)
    {
        $this->name   = $name;
        $this->time   = $time;
        $this->format = $format ?? new HttpDateFormat();
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return $this->format->format($this->time);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->time;
    }
}
