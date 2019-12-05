<?php

namespace Woof\Http;

use Woof\System\Clock;
use Woof\System\DefaultClock;
use InvalidArgumentException;

/**
 * HTTP-date 形式のヘッダー値の読み書きを行うクラスです。
 */
class HttpDateFormat
{
    /**
     * @var array
     */
    const SHORT_DAYS = [
        0 => "Sun",
        1 => "Mon",
        2 => "Tue",
        3 => "Wed",
        4 => "Thu",
        5 => "Fri",
        6 => "Sat",
    ];

    /**
     * @var array
     */
    const LONG_DAYS = [
        0 => "Sunday",
        1 => "Monday",
        2 => "Tuesday",
        3 => "Wednesday",
        4 => "Thursday",
        5 => "Friday",
        6 => "Saturday",
    ];

    /**
     * @var array
     */
    const MONTHS = [
        1  => "Jan",
        2  => "Feb",
        3  => "Mar",
        4  => "Apr",
        5  => "May",
        6  => "Jun",
        7  => "Jul",
        8  => "Aug",
        9  => "Sep",
        10 => "Oct",
        11 => "Nov",
        12 => "Dec",
    ];

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @param Clock $clock
     */
    public function __construct(Clock $clock = null)
    {
        $this->clock = $clock ?? DefaultClock::getInstance();
    }

    /**
     * 指定された HTTP-date 形式の文字列をシステム時刻に変換します。
     *
     * @param string $format
     * @return int
     * @throws InvalidArgumentException
     */
    public function parse(string $format): int
    {
        if (-1 !== ($rfc822 = $this->parseRfc822($format))) {
            return $rfc822;
        }
        if (-1 !== ($rfc850 = $this->parseRfc850($format))) {
            return $rfc850;
        }
        if (-1 !== ($ansi = $this->parseAnsi($format))) {
            return $ansi;
        }
        throw new InvalidArgumentException("Invalid format: '{$format}'");
    }

    /**
     * @param string $format
     * @return int
     */
    private function parseRfc822(string $format): int
    {
        $days    = implode("|", self::SHORT_DAYS);
        $months  = implode("|", self::MONTHS);
        $matched = [];
        if (preg_match("/\\A({$days}), ([0-3][0-9]) ({$months}) ([0-9]{4}) ([0-2][0-9]):([0-5][0-9]):([0-5][0-9]) GMT\\z/", $format, $matched)) {
            $year   = (int) $matched[4];
            $month  = array_search($matched[3], self::MONTHS);
            $day    = (int) $matched[2];
            $hour   = (int) $matched[5];
            $minute = (int) $matched[6];
            $second = (int) $matched[7];
            return gmmktime($hour, $minute, $second, $month, $day, $year);
        } else {
            return -1;
        }
    }

    /**
     * @param string $format
     * @return int
     */
    private function parseRfc850(string $format): int
    {
        $days    = implode("|", self::LONG_DAYS);
        $months  = implode("|", self::MONTHS);
        $matched = [];
        if (preg_match("/\\A({$days}), ([0-3][0-9])-({$months})-([0-9]{2}) ([0-2][0-9]):([0-5][0-9]):([0-5][0-9]) GMT\\z/", $format, $matched)) {
            $shortY = (int) $matched[4];
            $year   = $this->calculateFullYear($shortY);
            $month  = array_search($matched[3], self::MONTHS);
            $day    = (int) $matched[2];
            $hour   = (int) $matched[5];
            $minute = (int) $matched[6];
            $second = (int) $matched[7];
            return gmmktime($hour, $minute, $second, $month, $day, $year);
        } else {
            return -1;
        }
    }

    /**
     * @param string $format
     * @return int
     */
    private function parseAnsi(string $format): int
    {
        $days    = implode("|", self::SHORT_DAYS);
        $months  = implode("|", self::MONTHS);
        $matched = [];
        if (preg_match("/^({$days}) ({$months}) ([0-3 ][0-9]) ([0-2][0-9]):([0-5][0-9]):([0-5][0-9]) ([0-9]{4})$/", $format, $matched)) {
            $year   = (int) $matched[7];
            $month  = array_search($matched[2], self::MONTHS);
            $day    = (int) trim($matched[3]);
            $hour   = (int) $matched[4];
            $minute = (int) $matched[5];
            $second = (int) $matched[6];
            return gmmktime($hour, $minute, $second, $month, $day, $year);
        } else {
            return -1;
        }
    }

    /**
     * @param int $y
     * @return int
     */
    private function calculateFullYear(int $y): int
    {
        $currentYear = (int) date("Y", $this->clock->getTime());
        $century     = (int) ($currentYear / 100);
        $smallY      = $currentYear % 100;
        $resultC     = ($smallY < $y) ? $century - 1 : $century;
        return $resultC * 100 + $y;
    }

    /**
     * @param int $time
     * @return string
     */
    public function format(int $time): string
    {
        $n = (int) gmdate("n", $time);
        $w = (int) gmdate("w", $time);

        $year  = gmdate("Y", $time);
        $month = self::MONTHS[$n];
        $date  = gmdate("d", $time);
        $day   = self::SHORT_DAYS[$w];
        $hours = gmdate("H:i:s", $time);
        return "{$day}, {$date} {$month} {$year} {$hours} GMT";
    }
}
