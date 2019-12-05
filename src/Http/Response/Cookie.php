<?php

namespace Woof\Http\Response;

class Cookie
{
    /**
     * @var CookieAttributes
     */
    private $attr;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     * @param CookieAttributes $attr
     */
    public function __construct(string $name, string $value, CookieAttributes $attr = null)
    {
        $this->name  = $name;
        $this->value = $value;
        $this->attr  = $attr ?? self::getEmptyAttributes();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->attr->getDomain();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->attr->getPath();
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->attr->getExpires();
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->attr->isSecure();
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->attr->isHttpOnly();
    }

    /**
     * @return string
     */
    public function getSameSite(): string
    {
        return $this->attr->getSameSite();
    }

    /**
     * @return CookieAttributes
     * @codeCoverageIgnore
     */
    private static function getEmptyAttributes(): CookieAttributes
    {
        static $attr = null;
        if ($attr === null) {
            $attr = (new CookieAttributesBuilder())->build();
        }
        return $attr;
    }
}
