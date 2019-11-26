<?php

namespace Woof\Util;

use InvalidArgumentException;

class ArrayProperties implements Properties
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return array
     * @throws InvalidArgumentException
     */
    private function parseSegments(string $name): array
    {
        if (!strlen($name)) {
            throw new InvalidArgumentException("Config key is not specified");
        }

        $segments = explode(".", $name);
        foreach ($segments as $s) {
            if (!preg_match("/\\A[a-zA-Z0-9_\\-]+\\z/", $s)) {
                throw new InvalidArgumentException("Invalid config key: '{$name}'");
            }
        }
        return $segments;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function contains(string $name): bool
    {
        $segments = $this->parseSegments($name);
        return $this->checkBySegments($this->data, $segments);
    }

    /**
     * @param array $arr
     * @param array $segments
     * @return bool
     */
    private function checkBySegments(array $arr, array $segments): bool
    {
        $key = array_shift($segments);
        if (!count($segments)) {
            return array_key_exists($key, $arr);
        }

        $next = $arr[$key];
        return is_array($next) ? $this->checkBySegments($next, $segments) : false;
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get(string $name, $defaultValue = null)
    {
        $segments = $this->parseSegments($name);
        return $this->fetchBySegments($this->data, $segments, $defaultValue);
    }

    /**
     * @param array $arr
     * @param array $segments
     * @param mixed $defaultValue
     * @return mixed
     */
    private function fetchBySegments(array $arr, array $segments, $defaultValue)
    {
        $key = array_shift($segments);
        if (!array_key_exists($key, $arr)) {
            return $defaultValue;
        }

        $result = $arr[$key];
        if (!count($segments)) {
            return $result;
        }
        return is_array($result) ? $this->fetchBySegments($result, $segments, $defaultValue) : $defaultValue;
    }
}
