<?php

namespace Woof\Http;

use Woof\Http\Response\Body;
use Woof\Http\Response\Cookie;
use Woof\Http\Response\EmptyBody;

class Response
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

    private function __construct()
    {
        $this->headerList = [];
        $this->cookieList = [];
    }

    /**
     * このメソッドは ResponseBuilder::build() から参照されます。
     *
     * @param ResponseBuilder $builder
     * @return ResponseBuilder
     * @ignore
     */
    public static function newInstance(ResponseBuilder $builder): self
    {
        $body       = $builder->getBody();
        $headerList = $builder->getHeaderList();
        if ($body !== EmptyBody::getInstance()) {
            $headerList["content-type"]   = new TextField("Content-Type", $body->getContentType());
            $headerList["content-length"] = new TextField("Content-Length", $body->getContentLength());
        }

        $res             = new Response();
        $res->body       = $body;
        $res->status     = $builder->getStatus();
        $res->headerList = $headerList;
        $res->cookieList = $builder->getCookieList();
        return $res;
    }

    /**
     * @return Body
     */
    public function getBody(): Body
    {
        return $this->body;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * 指定された名前のヘッダーが存在するかどうか調べます。
     * ヘッダー名の大文字・小文字は区別されません。
     *
     * @param string $name ヘッダー名
     * @return bool 存在する場合のみ true
     */
    public function hasHeader(string $name): bool
    {
        $key = strtolower($name);
        return array_key_exists($key, $this->headerList);
    }

    /**
     * 指定された名前のヘッダーを返します。
     * もしも指定されたヘッダーが存在しない場合は EmptyField を返します。
     * ヘッダー名の大文字・小文字は区別されません。
     *
     * @param string $name
     * @return HeaderField
     */
    public function getHeader(string $name): HeaderField
    {
        $key = strtolower($name);
        return $this->headerList[$key] ?? EmptyField::getInstance();
    }

    /**
     * @return HeaderField[]
     */
    public function getHeaderList(): array
    {
        return array_values($this->headerList);
    }

    /**
     * @return Cookie[]
     */
    public function getCookieList(): array
    {
        return $this->cookieList;
    }
}
