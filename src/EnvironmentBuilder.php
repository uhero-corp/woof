<?php

namespace Woof;

use Woof\Log\Logger;
use Woof\System\Clock;
use Woof\System\DefaultClock;
use Woof\System\DefaultRandom;
use Woof\System\Random;
use Woof\System\Variables;
use Woof\Util\FileProperties;

abstract class EnvironmentBuilder
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
     * @param Config $config
     * @return EnvironmentBuilder
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param string $dirname
     * @return EnvironmentBuilder
     */
    public function setConfigDir(string $dirname): self
    {
        $this->config = new Config(new FileProperties($dirname));
        return $this;
    }

    /**
     * @return bool
     */
    public function hasConfig(): bool
    {
        return ($this->config !== null);
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Resources $resources
     * @return EnvironmentBuilder
     */
    public function setResources(Resources $resources): self
    {
        $this->resources = $resources;
        return $this;
    }

    /**
     * @param string $dirname
     * @return EnvironmentBuilder
     */
    public function setResourcesDir(string $dirname): self
    {
        $this->resources = new FileResources($dirname);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasResources(): bool
    {
        return ($this->resources !== null);
    }

    /**
     * @return Resources
     */
    public function getResources(): Resources
    {
        return $this->resources ?? NullResources::getInstance();
    }

    /**
     * @param DataStorage $dataStorage
     * @return EnvironmentBuilder
     */
    public function setDataStorage(DataStorage $dataStorage): self
    {
        $this->dataStorage = $dataStorage;
        return $this;
    }

    /**
     * @param string $dirname
     * @return EnvironmentBuilder
     */
    public function setDataStorageDir(string $dirname): self
    {
        $this->dataStorage = new FileDataStorage($dirname);
        return $this;
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
        return $this->dataStorage;
    }

    /**
     * @param Logger $logger
     * @return EnvironmentBuilder
     */
    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasLogger(): bool
    {
        return ($this->logger !== null);
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger ?? Logger::getNopLogger();
    }

    /**
     * @param Clock $clock
     * @return EnvironmentBuilder
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
     * @return EnvironmentBuilder
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
     *
     * @param Variables $variables
     * @return EnvironmentBuilder
     */
    public function setVariables(Variables $variables): self
    {
        $this->variables = $variables;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasVariables(): bool
    {
        return ($this->variables !== null);
    }

    /**
     * @return Variables
     */
    public function getVariables(): Variables
    {
        return $this->variables ?? Variables::getDefaultInstance();
    }
}
