<?php

namespace Woof\Log;

class DefaultLogFormat implements LogFormat
{
    /**
     * date() の引数として使用されるフォーマットです
     *
     * @var string
     */
    private $dateFormat;

    /**
     *
     * @param string $dateFormat date() 関数で使用可能なフォーマット文字列
     */
    public function __construct(string $dateFormat = "")
    {
        $this->dateFormat = strlen($dateFormat) ? $dateFormat : "Y-m-d H:i:s";
    }

    /**
     *
     * @param string $message
     * @param int $time
     * @param int $level
     * @return string
     */
    public function format(string $message, int $time, int $level): string
    {
        $label = $this->formatLogLevel($level);
        $date  = date($this->dateFormat, $time);
        return "[{$date}][{$label}] {$message}";
    }

    /**
     * 指定された定数に応じたラベル ("ERROR", "INFO" など) を返します。
     *
     * @param int $level
     * @return string
     */
    private function formatLogLevel(int $level): string
    {
        static $labels = [
            Logger::LEVEL_ERROR => "ERROR",
            Logger::LEVEL_ALERT => "ALERT",
            Logger::LEVEL_INFO  => "INFO ",
            Logger::LEVEL_DEBUG => "DEBUG",
        ];
        return $labels[$level];
    }
}
