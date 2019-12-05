<?php

namespace Woof\Log;

use InvalidArgumentException;
use Woof\DataStorage;

class DataLogStorage implements LogStorage
{
    /**
     * @var DataStorage
     */
    private $dataStorage;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param DataStorage $data
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(DataStorage $data, string $prefix = "app", string $suffix = ".log")
    {
        if (!strlen($prefix)) {
            throw new InvalidArgumentException("Prefix is required");
        }
        $this->dataStorage = $data;
        $this->prefix      = $prefix;
        $this->suffix      = $suffix;
    }

    /**
     * @param string $content
     * @param int $time
     * @param int $level
     * @return bool
     */
    public function write(string $content, int $time, int $level): bool
    {
        return $this->dataStorage->append($this->formatKey($time), $content . PHP_EOL);
    }

    /**
     * @param int $time
     * @return string
     */
    private function formatKey(int $time): string
    {
        $datePart = date("Ymd", $time);
        return "{$this->prefix}-{$datePart}{$this->suffix}";
    }
}
