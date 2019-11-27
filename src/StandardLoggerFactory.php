<?php

namespace Woof;

use Woof\Log\DataLogStorage;
use Woof\Log\DefaultLogFormat;
use Woof\Log\Logger;
use Woof\Log\LoggerBuilder;

class StandardLoggerFactory
{
    /**
     * @param Config $config
     * @param DataStorage $data
     * @return Logger
     */
    public function create(Config $config, DataStorage $data = null): Logger
    {
        if ($data === null || !$config->contains("logger")) {
            return Logger::getNopLogger();
        }
        $sub      = $config->getSubConfig("logger");
        $dirname  = $sub->getString("dirname", "logs");
        $prefix   = $sub->getString("prefix", "app");
        $format   = $sub->getString("format", "");
        $logLevel = $sub->getString("loglevel", "error");
        $multiple = $sub->getBool("multiple");
        return (new LoggerBuilder())
            ->setStorage(new DataLogStorage($data, "{$dirname}/{$prefix}"))
            ->setFormat(new DefaultLogFormat($format))
            ->setLogLevel($this->detectLogLevel($logLevel))
            ->setMultiple($multiple)
            ->build();
    }

    /**
     * @param string $logLevel
     * @return int
     */
    private function detectLogLevel(string $logLevel): int
    {
        $validList = [
            "error" => Logger::LEVEL_ERROR,
            "alert" => Logger::LEVEL_ALERT,
            "info"  => Logger::LEVEL_INFO,
            "debug" => Logger::LEVEL_DEBUG,
        ];
        $key = strtolower($logLevel);
        return $validList[$key] ?? Logger::LEVEL_ERROR;
    }
}
