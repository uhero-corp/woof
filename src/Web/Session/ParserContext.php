<?php

namespace Woof\Web\Session;

class ParserContext
{
    /**
     * @var string
     */
    private $source;

    /**
     * @var int
     */
    private $index;

    /**
     * @var array
     */
    private $result;

    /**
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
        $this->index  = 0;
        $this->result = [];
    }

    /**
     * @return array
     */
    public function parse(): array
    {
        $length = strlen($this->source);
        while ($this->index < $length) {
            $this->next();
        }
        return $this->result;
    }

    private function next(): void
    {
        $current = substr($this->source, $this->index);
        $matched = [];
        if (!preg_match("/\\A([^|]+)\\|/", $current, $matched)) {
            throw new ParseException("Invalid session format");
        }

        $this->index += strlen($matched[0]);
        $key   = $matched[1];
        $value = $this->unserialize();
        $this->result[$key] = $value;
    }

    /**
     * @return mixed
     * @throws ParseException
     */
    private function unserialize()
    {
        $current = substr($this->source, $this->index);
        $matched = [];
        if (substr($current, 0, 2) === "N;") {
            $this->index += 2;
            return null;
        }
        if (substr($current, 0, 4) === "b:0;") {
            $this->index += 4;
            return false;
        }
        if (substr($current, 0, 4) === "b:1;") {
            $this->index += 4;
            return true;
        }
        if (preg_match("/\\Ai:([0-9\\-]+);/", $current, $matched)) {
            $this->index += strlen($matched[0]);
            return (int) $matched[1];
        }
        if (preg_match("/\\Ad:([0-9\\.\\-]+);/", $current, $matched)) {
            $this->index += strlen($matched[0]);
            return (float) $matched[1];
        }
        if (preg_match("/\\As:([0-9]+):/", $current, $matched)) {
            $this->index += strlen($matched[0]);
            $length = $matched[1];
            $result = substr($this->source, $this->index, $length + 3);
            if (substr($result, 0, 1) !== '"' || substr($result, -2) !== '";') {
                throw new ParseException("Invalid session format");
            }
            $this->index += $length + 3;
            return substr($result, 1, -2);
        }
        if (preg_match("/\\Aa:([0-9]+):{/", $current, $matched)) {
            $this->index += strlen($matched[0]);
            $count = (int) $matched[1];
            $result = [];
            for ($i = 0; $i < $count; $i++) {
                $key   = $this->unserialize();
                $value = $this->unserialize();
                $result[$key] = $value;
            }
            $this->index++;
            return $result;
        }

        throw new ParseException("Invalid session format (index:{$this->index})");
    }
}
