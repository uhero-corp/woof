<?php

namespace Woof\Util;

class JsonDecoder implements StringDecoder
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
        $result = json_decode($src, true);
        return is_array($result) ? $result : [];
    }

    /**
     * @return JsonDecoder
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
