<?php

namespace Woof;

use InvalidArgumentException;
use LogicException;
use Woof\Log\Logger;
use Woof\System\Clock;
use Woof\System\Random;
use Woof\System\Variables;

abstract class Environment
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Resources
     */
    private $resources;

    /**
     * @var DataStorage
     */
    private $dataStorage;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var Random
     */
    private $random;

    /**
     * @var Variables
     */
    private $variables;

    /**
     * @param EnvironmentBuilder $builder
     */
    protected function init(EnvironmentBuilder $builder): void
    {
        if (!$builder->hasConfig()) {
            throw new LogicException("Config is not specified");
        }
        $config    = $builder->getConfig();
        $resources = $builder->getResources();
        $data      = $builder->hasDataStorage() ? $builder->getDataStorage() : null;
        $logger    = $builder->hasLogger() ? $builder->getLogger() : (new StandardLoggerFactory())->create($config, $data);

        $this->config      = $config;
        $this->resources   = $resources;
        $this->dataStorage = $data;
        $this->logger      = $logger;
        $this->clock       = $builder->getClock();
        $this->random      = $builder->getRandom();
        $this->variables   = $builder->getVariables();
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Resources
     */
    public function getResources(): Resources
    {
        return $this->resources;
    }

    /**
     * @return bool
     */
    public function hasDataStorage(): bool
    {
        return ($this->dataStorage !== null);
    }

    /**
     * @return DataStorage
     */
    public function getDataStorage(): DataStorage
    {
        if ($this->dataStorage === null) {
            throw new LogicException("DataStorage is not set");
        }
        return $this->dataStorage;
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return Clock
     */
    public function getClock()
    {
        return $this->clock;
    }

    /**
     * @return int
     */
    public function now()
    {
        return $this->clock->getTime();
    }

    /**
     * @return Random
     */
    public function getRandom()
    {
        return $this->random;
    }

    /**
     * 次の乱数値を整数で取得します。
     *
     * @param int $min 返される乱数値の最小値 (デフォルトは 0)
     * @param int $max 返される乱数値の最大値 (デフォルトは mt_getrandmax())
     * @return int 次の乱数値
     * @throws InvalidArgumentException $max が $min よりも小さい場合
     */
    public function rand(int $min = null, int $max = null): int
    {
        // @codeCoverageIgnoreStart
        static $randMax = null;
        if ($randMax === null) {
            $randMax = mt_getrandmax();
        }
        // @codeCoverageIgnoreEnd

        $random = $this->getRandom();
        if ($min === null || $max === null) {
            return $random->next();
        }

        if ($max < $min) {
            throw new InvalidArgumentException("max({$max}) is smaller than min({$min})");
        }
        $rand = (int) ($random->next() / $randMax * (1 + $max - $min));
        return min($min + $rand, $max);
    }

    /**
     * @return Variables
     */
    public function getVariables(): Variables
    {
        return $this->variables;
    }
}
