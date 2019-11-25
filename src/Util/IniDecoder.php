<?php

namespace Woof\Util;

class IniDecoder implements StringDecoder
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {

    }

    /**
     * @param string $src
     * @return array
     */
    public function parse(string $src): array
    {
        $result = parse_ini_string($src, true, INI_SCANNER_TYPED);
        return is_array($result) ? $result : [];
    }

    /**
     * @return IniDecoder
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
}
