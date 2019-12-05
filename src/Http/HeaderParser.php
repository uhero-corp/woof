<?php

namespace Woof\Http;

class HeaderParser
{
    /**
     * 値が quality values となるヘッダー名の一覧です。
     *
     * @var array
     */
    private $qNames;

    /**
     * 値が HTTP-date となるヘッダー名の一覧です。
     *
     * @var array
     */
    private $dNames;

    /**
     * @var HttpDateFormat
     */
    private $format;

    /**
     * @param string[] $qNames
     * @param string[] $dNames
     * @param HttpDateFormat $format
     */
    public function __construct(array $qNames = [], array $dNames = [], HttpDateFormat $format = null)
    {
        $rawQNames = count($qNames) ? $qNames : self::getDefaultQualityValuesNames();
        $rawDNames = count($dNames) ? $dNames : self::getDefaultHttpDateNames();

        $this->qNames = array_map("strtolower", $rawQNames);
        $this->dNames = array_map("strtolower", $rawDNames);
        $this->format = $format ?? new HttpDateFormat();
    }

    /**
     * @return array
     */
    public static function getDefaultQualityValuesNames(): array
    {
        return [
            "accept",
            "accept-charset",
            "accept-encoding",
            "accept-language",
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultHttpDateNames(): array
    {
        return [
            "date",
            "if-modified-since",
            "last-modified",
        ];
    }

    /**
     * @param string $name
     * @param string $value
     * @return HeaderField
     */
    public function parse(string $name, string $value): HeaderField
    {
        $lName = strtolower($name);
        $uName = ucwords($name, " -");
        if (in_array($lName, $this->qNames)) {
            return new QualityValues($uName, $this->parseQualityValues($value));
        }
        if (in_array($lName, $this->dNames)) {
            $format = $this->format;
            $time   = $format->parse($value);
            return new HttpDate($uName, $time, $format);
        }

        return new TextField($uName, $value);
    }

    /**
     * qualitiy values を配列に変換します.
     *
     * @param  string $str
     * @return array
     */
    private function parseQualityValues(string $str)
    {
        $values  = preg_split("/\\s*,\\s*/", $str);
        $matched = [];
        $qvList  = [];
        foreach ($values as $item) {
            if (preg_match("/\\A([^;]+)\\s*;\\s*(.+)\\z/", $item, $matched)) {
                $key    = $matched[1];
                $qvalue = self::parseQvalue($matched[2]);
            } else {
                $key    = $item;
                $qvalue = 1.0;
            }
            $qvList[$key] = $qvalue;
        }
        return $qvList;
    }

    /**
     * qvalue 形式の文字列 ("q=0.9" など) に含まれる小数部分を float に変換します。
     *
     * @param string $qvalue "q=0.9" のような形式の文字列
     * @return float 変換後の qvalue の値。もしも変換に失敗した場合は 1.0
     */
    private function parseQvalue(string $qvalue): float
    {
        $matched = [];
        if (preg_match("/\\Aq\\s*=\\s*([0-9\\.]+)\\z/", $qvalue, $matched)) {
            $val = (float) $matched[1];
            return (0.0 < $val && $val <= 1.0) ? $val : 1.0;
        }
        return 1.0;
    }
}
