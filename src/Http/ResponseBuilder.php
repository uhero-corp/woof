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

    /**
     * @param Response $response
     */
    public function __construct(Response $response = null)
    {
        $this->headerList = [];
        $this->cookieList = [];
        if ($response !== null) {
            $this->importResponse($response);
        }
    }

    /**
     * @param Response $response
     */
    private function importResponse(Response $response): void
    {
        $body = $response->getBody();
        if ($body !== EmptyBody::getInstance()) {
            $this->body = $body;
        }

        $ignoreList = ["Content-Type", "Content-Length"];
        foreach ($response->getHeaderList() as $header) {
            if (!in_array($header->getName(), $ignoreList)) {
                $this->setHeader($header);
            }
        }

        $this->status     = $response->getStatus();
        $this->cookieList = $response->getCookieList();
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

    /**
     * @return Response
     */
    public function build(): Response
    {
        return Response::newInstance($this);
    }
}
