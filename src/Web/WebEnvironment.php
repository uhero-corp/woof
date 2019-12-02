<?php

namespace Woof\Web;

use LogicException;
use Woof\Config;
use Woof\Environment;
use Woof\Http\HeaderParser;
use Woof\Http\Request;
use Woof\Http\RequestLoader;
use Woof\Log\Logger;
use Woof\System\Variables;
use Woof\Web\Session\SessionStorage;

class WebEnvironment extends Environment
{
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * @var Context
     */
    private $context;

    /**
     *
     * @var Request
     */
    private $clientRequest;

    /**
     * このクラスは EnvironmentBuilder を使用して初期化します。
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {

    }

    /**
     * @param WebEnvironmentBuilder $builder
     * @return WebEnvironment
     * @throws LogicException
     */
    public static function newInstance(WebEnvironmentBuilder $builder): self
    {
        $instance = new self();
        $instance->init($builder);

        $config = $instance->getConfig();
        $data   = $instance->hasDataStorage() ? $instance->getDataStorage() : null;
        $logger = $instance->getLogger();
        $parser = $builder->hasHeaderParser() ? $parser : null;
        $sess   = $builder->hasSessionStorage() ? $builder->getSessionStorage() : (new StandardSessionStorageFactory())->create($config, $data, $logger);

        $instance->sessionStorage = $sess;
        $instance->context        = self::createContext($config);
        $instance->clientRequest  = self::createClientRequest($instance->getVariables(), $logger, $parser);
        return $instance;
    }

    /**
     * @param Config $config
     * @return Context
     */
    private static function createContext(Config $config): Context
    {
        $rootPath  = $config->getString("app.root-path");
        $separator = $config->getString("app.arg-separator");
        return new Context($rootPath, $separator);
    }

    /**
     * @param Variables $var
     * @param Logger $logger
     * @param HeaderParser $parser
     * @return Request
     */
    private static function createClientRequest(Variables $var, Logger $logger, HeaderParser $parser = null)
    {
        return (new RequestLoader($logger, $parser))->load($var);
    }

    /**
     * @return SessionStorage
     */
    public function getSessionStorage(): SessionStorage
    {
        return $this->sessionStorage;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return Request
     */
    public function getClientRequest(): Request
    {
        return $this->clientRequest;
    }
}
