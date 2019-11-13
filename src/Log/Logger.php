<?php

namespace Woof\Log;

use Woof\System\Clock;
use Woof\System\DefaultClock;

class Logger
{
    /**
     * @var int
     */
    const LEVEL_ERROR = 0;

    /**
     * @var int
     */
    const LEVEL_ALERT = 1;

    /**
     * @var int
     */
    const LEVEL_INFO  = 2;

    /**
     * @var int
     */
    const LEVEL_DEBUG = 3;

    /**
     * @see Logger::LEVEL_ERROR
     * @see Logger::LEVEL_ALERT
     * @see Logger::LEVEL_INFO
     * @see Logger::LEVEL_DEBUG
     * @var int
     */
    private $logLevel;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var LogFormat
     */
    private $format;

    /**
     * @var LogStorage
     */
    private $storage;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * Logger クラスは LoggerBuilder を使用して構築するため、直接インスタンス化することはできません。
     */
    private function __construct()
    {

    }

    /**
     * このメソッドは LoggerBuilder::build() から参照されます。
     *
     * @param LoggerBuilder $builder
     * @return Logger
     * @ignore
     */
    public static function newInstance(LoggerBuilder $builder): self
    {
        $instance           = new self();
        $instance->logLevel = $builder->getLogLevel();
        $instance->multiple = $builder->getMultiple();
        $instance->format   = $builder->getFormat();
        $instance->storage  = $builder->getStorage();
        $instance->clock    = $builder->getClock();
        return $instance;
    }

    /**
     * ログの書き込みを一切行わない Logger インスタンスを返します。
     *
     * @return Logger
     */
    public static function getNopLogger(): self
    {
        // @codeCoverageIgnoreStart
        static $instance = null;
        if ($instance === null) {
            $instance           = new self();
            $instance->logLevel = -1;
            $instance->multiple = false;
            $instance->format   = new DefaultLogFormat();
            $instance->storage  = NullLogStorage::getInstance();
            $instance->clock    = DefaultClock::getInstance();
        }
        // @codeCoverageIgnoreEnd
        return $instance;
    }

    /**
     * この Logger に設定されたログレベルを返します。
     *
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }

    /**
     * 複数行のログの処理方法を確認します。
     *
     * @return bool 複数行の文字列を一度に処理する場合は true, 行単位でログに追記する場合は false
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * @return LogFormat
     */
    public function getFormat(): LogFormat
    {
        return $this->format;
    }

    /**
     *
     * @return LogStorage
     */
    public function getStorage(): LogStorage
    {
        return $this->storage;
    }

    /**
     *
     * @return Clock
     */
    public function getClock(): Clock
    {
        return $this->clock;
    }

    /**
     * 指定されたログをレベル ERROR で記録します。
     *
     * @param mixed $value
     * @return bool
     */
    public function error($value): bool
    {
        return $this->log($value, self::LEVEL_ERROR);
    }

    /**
     * 指定されたログをレベル ALERT で記録します。
     * この Logger に設定されているログレベルが ERROR の場合は無視されます。
     *
     * @param mixed $value
     * @return bool
     */
    public function alert($value): bool
    {
        return $this->log($value, self::LEVEL_ALERT);
    }

    /**
     * 指定されたログをレベル INFO で記録します。
     * この Logger に設定されているログレベルが ERROR, ALERT の場合は無視されます。
     *
     * @param mixed $value
     * @return bool
     */
    public function info($value): bool
    {
        return $this->log($value, self::LEVEL_INFO);
    }

    /**
     * 指定されたログをレベル DEBUG で記録します。
     * この Logger に設定されているログレベルが DEBUG 以外の場合は無視されます。
     *
     * @param mixed $value
     * @return bool
     */
    public function debug($value): bool
    {
        return $this->log($value, self::LEVEL_DEBUG);
    }

    /**
     * @param mixed $value
     * @param int $level
     * @return bool
     */
    private function log($value, int $level): bool
    {
        if ($this->logLevel < $level) {
            return true;
        }
        if (!is_string($value)) {
            return $this->log($this->getStringValue($value), $level);
        }
        $time   = $this->clock->getTime();
        $lines  = $this->multiple ? [$value] : preg_split("/\\r\\n|\\r|\\n/", $value);
        $result = true;
        foreach ($lines as $line) {
            $content = $this->format->format($line, $time, $level);
            $result  = $this->storage->write($content, $time, $level) && $result;
        }
        return $result;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getStringValue($value): string
    {
        if (is_object($value) && method_exists($value, "__toString")) {
            return $value->__toString();
        }
        if (is_object($value) || is_array($value)) {
            return trim(print_r($value, true));
        }
        if ($value === null) {
            return "(NULL)";
        }
        if ($value === true) {
            return "(TRUE)";
        }
        if ($value === false) {
            return "(FALSE)";
        }
        return (string) $value;
    }
}
