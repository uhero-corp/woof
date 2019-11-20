<?php

namespace Woof\Http;

use Woof\Http\Response\Body;
use Woof\Http\Response\Cookie;
use Woof\Http\Response\CookieAttributes;
use Woof\Http\Response\EmptyBody;

class ResponseBuilder
{
    /**
     * @var Body
     */
    private $body;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var HeaderField[]
     */
    private $headerList;

    /**
     * @var Cookie[]
     */
    private $cookieList;

    public function __construct()
    {
        $this->headerList = [];
        $this->cookieList = [];
    }

    /**
     * @param Body $body
     * @return ResponseBuilder このオブジェクト
     */
    public function setBody(Body $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return Body
     */
    public function getBody(): Body
    {
        return $this->body ?? EmptyBody::getInstance();
    }

    /**
     * @param Status $status
     * @return ResponseBuilder このオブジェクト
     */
    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return ($this->status === null) ? Status::getOK() : $this->status;
    }

    /**
     * @return bool
     */
    public function hasStatus(): bool
    {
        return ($this->status !== null);
    }

    /**
     * @param HeaderField $header
     * @return ResponseBuilder このオブジェクト
     */
    public function setHeader(HeaderField $header): self
    {
        if ($header === EmptyField::getInstance()) {
            return $this;
        }

        $name = strtolower($header->getName());

        $this->headerList[$name] = $header;
        return $this;
    }

    /**
     * @return HeaderField[]
     */
    public function getHeaderList(): array
    {
        return $this->headerList;
    }

    /**
     * @param string $name
     * @param string $value
     * @param CookieAttributes $attr
     * @return ResponseBuilder このオブジェクト
     */
    public function setCookie(string $name, string $value, CookieAttributes $attr = null): self
    {
        $this->cookieList[$name] = new Cookie($name, $value, $attr);
        return $this;
    }

    /**
     * @return Cookie[]
     */
    public function getCookieList(): array
    {
        return $this->cookieList;
    }
}
