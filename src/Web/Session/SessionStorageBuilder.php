<?php

namespace Woof\Web\Session;

use InvalidArgumentException;
use Woof\System\Clock;
use Woof\System\DefaultClock;
use Woof\System\DefaultRandom;
use Woof\System\Random;

class SessionStorageBuilder
{
    /**
     * @var SessionContainer
     */
    private $container;

    /**
     * @var string
     */
    private $key;

    /**
     * @var int
     */
    private $maxAge;

    /**
     * @var float
     */
    private $gcProbaility;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var Random
     */
    private $random;

    /**
     * @param SessionContainer $container
     * @return SessionStorageBuilder
     */
    public function setSessionContainer(SessionContainer $container): self
    {
        $this->container = $container;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSessionContainer(): bool
    {
        return ($this->container !== null);
    }

    /**
     * @return SessionContainer
     */
    public function getSessionContainer(): SessionContainer
    {
        return $this->container;
    }

    /**
     * @param string $key
     * @return SessionStorageBuilder
     */
    public function setKey(string $key): self
    {
        if (!preg_match("/\\A[a-zA-Z0-9_\\.\\-]+\\z/", $key)) {
            throw new InvalidArgumentException("Invalid session key: '{$key}'");
        }
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key ?? "";
    }

    /**
     * @param int $maxAge
     * @return SessionStorageBuilder このオブジェクト
     */
    public function setMaxAge(int $maxAge): self
    {
        if ($maxAge <= 0) {
            throw new InvalidArgumentException("Invalid max-age value: {$maxAge}");
        }
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxAge(): int
    {
        return $this->maxAge ?? 1800;
    }

    /**
     * ガベージコレクション (期限切れセッションの削除) を実行する確率を 0 以上 1 以下で指定します。
     * 0 の場合はガベージコレクションを行いません。
     * 1 の場合はセッションを読み込むタイミングで常にガベージコレクションを行います。
     *
     * @param float $p 0 以上 1 以下の小数
     * @return SessionStorageBuilder このオブジェクト
     */
    public function setGcProbability(float $p): self
    {
        if ($p < 0.0 || 1.0 < $p) {
            throw new InvalidArgumentException("Invalid GC probability value: {$p}");
        }
        $this->gcProbaility = $p;
        return $this;
    }

    /**
     * ガベージコレクションの実行確率を返します。
     * 未指定の場合は 0 となります。
     *
     * @return float
     */
    public function getGcProbability(): float
    {
        return $this->gcProbaility ?? 0.0;
    }

    /**
     * @param Clock $clock
     * @return SessionStorageBuilder このオブジェクト
     */
    public function setClock(Clock $clock): self
    {
        $this->clock = $clock;
        return $this;
    }

    /**
     * @return Clock
     */
    public function getClock(): Clock
    {
        return $this->clock ?? DefaultClock::getInstance();
    }

    /**
     * @param Random $random
     * @return SessionStorageBuilder このオブジェクト
     */
    public function setRandom(Random $random): self
    {
        $this->random = $random;
        return $this;
    }

    /**
     * @return Random
     */
    public function getRandom(): Random
    {
        return $this->random ?? DefaultRandom::getInstance();
    }

    /**
     * @return SessionStorage
     */
    public function build(): SessionStorage
    {
        return SessionStorage::newInstance($this);
    }
}
