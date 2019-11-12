<?php

namespace Woof\Log;

use InvalidArgumentException;
use Woof\System\Clock;
use Woof\System\DefaultClock;

class LoggerBuilder
{
    /**
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
     * このオブジェクトのログレベルを設定します。
     *
     * @param int $logLevel ログレベル定数
     * @return LoggerBuilder このオブジェクト
     * @see Logger::LEVEL_ERROR
     * @see Logger::LEVEL_ALERT
     * @see Logger::LEVEL_INFO
     * @see Logger::LEVEL_DEBUG
     */
    public function setLogLevel(int $logLevel): self
    {
        $validList = [
            Logger::LEVEL_ERROR,
            Logger::LEVEL_ALERT,
            Logger::LEVEL_INFO,
            Logger::LEVEL_DEBUG,
        ];
        if (!in_array($logLevel, $validList)) {
            throw new InvalidArgumentException("Invalid log level: {$logLevel}");
        }

        $this->logLevel = $logLevel;
        return $this;
    }

    /**
     * このオブジェクトに設定されているログレベルを返します。
     *
     * @return int
     */
    public function getLogLevel(): int
    {
        return $this->logLevel ?? Logger::LEVEL_ERROR;
    }

    /**
     * 複数行の文字列を一度に処理する場合は true, 行単位でログに追記する場合は false を指定します。
     * 未指定 (デフォルト) の処理は false となります。
     *
     * @param bool $multiple
     * @return LoggerBuilder このオブジェクト
     */
    public function setMultiple(bool $multiple): self
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * 複数行のログの処理方法を確認します。
     *
     * @return bool 複数行の文字列を一度に処理する場合は true, 行単位でログに追記する場合は false
     */
    public function getMultiple(): bool
    {
        return $this->multiple ?? false;
    }

    /**
     * LogFormat を設定します。
     *
     * @param LogFormat $format
     * @return LoggerBuilder このオブジェクト
     */
    public function setFormat(LogFormat $format): self
    {
        $this->format = $format;
        return $this;
    }

    /**
     * このオブジェクトに設定されている LogFormat を返します。
     * LogFormat がセットされていない場合は "Y-m-d H:i:s" 形式の DefaultLogFormat インスタンスを返します。
     *
     * @return LogFormat
     */
    public function getFormat(): LogFormat
    {
        if ($this->format === null) {
            $this->format = new DefaultLogFormat();
        }
        return $this->format;
    }

    /**
     * LogStorage を設定します。
     *
     * @param LogStorage $storage
     * @return LoggerBuilder このオブジェクト
     */
    public function setStorage(LogStorage $storage): self
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * LogStorage が設定されているかどうかを調べます。
     *
     * @return bool LogStorage が設定済の場合のみ true
     */
    public function hasStorage(): bool
    {
        return ($this->storage !== null);
    }

    /**
     * このオブジェクトに設定されている LogStorage を返します。
     * 存在しない場合は NullLogStorage を返します。
     * 明示的にセットされているかどうかを判別するには hasStorage() を使用してください。
     *
     * @return LogStorage
     */
    public function getStorage(): LogStorage
    {
        return $this->storage ?? NullLogStorage::getInstance();
    }

    /**
     * このオブジェクトの Clock を設定します。
     *
     * @param Clock $clock
     * @return LoggerBuilder このオブジェクト
     */
    public function setClock(Clock $clock): self
    {
        $this->clock = $clock;
        return $this;
    }

    /**
     * このオブジェクトに設定されている Clock オブジェクトを返します。
     * 未設定の場合は DefaultClock を返します。
     *
     * @return Clock
     */
    public function getClock(): Clock
    {
        return $this->clock ?? DefaultClock::getInstance();
    }
}
