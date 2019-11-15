<?php

namespace Woof\Http\Response;

class CookieAttributes
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $expires;

    /**
     * @var boolean
     */
    private $secure;

    /**
     * @var bool
     */
    private $httpOnly;

    /**
     * @var string
     */
    private $sameSite;

    /**
     * このクラスは CookieAttributesBuilder を使用して構築するため、直接インスタンス化することはできません。
     */
    private function __construct()
    {

    }

    /**
     * このメソッドは CookieAttributesBuilder::build() から参照されます。
     *
     * @param CookieAttributesBuilder $builder
     * @return CookieAttributes
     * @ignore
     */
    public static function newInstance(CookieAttributesBuilder $builder): self
    {
        $instance           = new self();
        $instance->domain   = $builder->getDomain();
        $instance->path     = $builder->getPath();
        $instance->expires  = $builder->getExpires();
        $instance->secure   = $builder->isSecure();
        $instance->httpOnly = $builder->isHttpOnly();
        $instance->sameSite = $builder->getSameSite();
        return $instance;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @return string
     */
    public function getSameSite(): string
    {
        return $this->sameSite;
    }
}
