<?php

namespace Woof\Web;

use Woof\EnvironmentBuilder;
use Woof\Http\HeaderParser;
use Woof\Web\Session\SessionStorage;

class WebEnvironmentBuilder extends EnvironmentBuilder
{
    /**
     * @var SessionStorage
     */
    private $sessionStorage;

    /**
     * @var HeaderParser
     */
    private $headerParser;

    /**
     * @param SessionStorage $sessionStorage
     * @return WebEnvironmentBuilder このオブジェクト
     */
    public function setSessionStorage(SessionStorage $sessionStorage): self
    {
        $this->sessionStorage = $sessionStorage;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasSessionStorage(): bool
    {
        return ($this->sessionStorage !== null);
    }

    /**
     * @return SessionStorage
     */
    public function getSessionStorage(): SessionStorage
    {
        return $this->sessionStorage;
    }

    /**
     * @param HeaderParser $parser
     * @return WebEnvironmentBuilder このオブジェクト
     */
    public function setHeaderParser(HeaderParser $parser): self
    {
        $this->headerParser = $parser;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasHeaderParser(): bool
    {
        return ($this->headerParser !== null);
    }

    /**
     * @return HeaderParser
     */
    public function getHeaderParser(): HeaderParser
    {
        return $this->headerParser;
    }

    /**
     * @return WebEnvironment
     */
    public function build(): WebEnvironment
    {
        return WebEnvironment::newInstance($this);
    }
}
