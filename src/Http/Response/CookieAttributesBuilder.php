<?php

namespace Woof\Http\Response;

use InvalidArgumentException;

class CookieAttributesBuilder
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
     * "Strict", "Lax", "None" のいずれかの値を取ります。
     *
     * @var string
     */
    private $sameSite;

    /**
     * @param string $domain
     * @return CookieAttributesBuilder このオブジェクト
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain ?? "";
    }

    /**
     * @param string $path
     * @return CookieAttributesBuilder このオブジェクト
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path ?? "";
    }

    /**
     * @param int $expires
     * @return CookieAttributesBuilder このオブジェクト
     */
    public function setExpires(int $expires): self
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires ?? 0;
    }

    /**
     * @param bool $secure
     * @return CookieAttributesBuilder このオブジェクト
     */
    public function setSecure(bool $secure): self
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure ?? false;
    }

    /**
     * @param bool $httpOnly
     * @return CookieAttributesBuilder このオブジェクト
     */
    public function setHttpOnly(bool $httpOnly): self
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly ?? false;
    }

    /**
     * SameSite 属性を設定します。
     * 引数に指定可能な文字列は "Strict", "Lax", "None", 空文字列のいずれかとなります。
     * 空文字列を指定した場合 SameSite 属性は付与されなくなります。
     *
     * @param string $value
     * @return CookieAttributesBuilder このオブジェクト
     * @throws InvalidArgumentException 許可された値以外の文字列が指定された場合
     */
    public function setSameSite(string $value): self
    {
        $validList = ["Strict", "Lax", "None", ""];
        $subject   = ucfirst(strtolower($value));
        if (!in_array($subject, $validList)) {
            throw new InvalidArgumentException("Invalid SameSite value: '{$value}'");
        }
        $this->sameSite = $subject;
        return $this;
    }

    /**
     * SameSite 属性の値を返します。
     * セットされていない場合は空文字列を返します。
     *
     * @return string SameSite 属性の値
     */
    public function getSameSite(): string
    {
        return $this->sameSite ?? "";
    }

    /**
     * @return CookieAttributes
     */
    public function build(): CookieAttributes
    {
        return CookieAttributes::newInstance($this);
    }
}
