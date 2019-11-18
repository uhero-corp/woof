<?php

namespace Woof\Http;

use LogicException;

class RequestBuilder
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $method;

    /**
     * @var HeaderField[]
     */
    private $headerList;

    /**
     * @var array
     */
    private $queryList;

    /**
     * @var array
     */
    private $postList;

    /**
     * @var array
     */
    private $cookieList;

    /**
     * @var array
     */
    private $fileList;

    public function __construct()
    {
        $this->headerList = [];
        $this->queryList  = [];
        $this->postList   = [];
        $this->cookieList = [];
        $this->fileList   = [];
    }

    /**
     * @param string $host
     * @return RequestBuilder このオブジェクト
     */
    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host ?? "";
    }

    /**
     * @param string $uri
     * @return RequestBuilder このオブジェクト
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri ?? "";
    }

    /**
     * @param string $path
     * @return RequestBuilder このオブジェクト
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * アクセスされた URL のクエリを含まない部分を返します。
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path ?? "";
    }

    /**
     * @param string $scheme
     * @return RequestBuilder このオブジェクト
     */
    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme ?? "";
    }

    /**
     * @param string $method
     * @return RequestBuilder このオブジェクト
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method ?? "";
    }

    /**
     * @param HeaderField $header
     * @return RequestBuilder このオブジェクト
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
     * @param string|array $value
     * @return RequestBuilder このオブジェクト
     */
    public function setQuery(string $name, $value): self
    {
        $this->queryList[$name] = $value;
        return $this;
    }

    /**
     * @param array $queryList
     * @return RequestBuilder このオブジェクト
     */
    public function setQueryList(array $queryList): self
    {
        $this->queryList = array_merge($this->queryList, $queryList);
        return $this;
    }

    /**
     * @return array
     */
    public function getQueryList(): array
    {
        return $this->queryList;
    }

    /**
     * @param string $name
     * @param string|array $value
     * @return RequestBuilder このオブジェクト
     */
    public function setPost(string $name, $value): self
    {
        $this->postList[$name] = $value;
        return $this;
    }

    /**
     * @param array $postList
     * @return RequestBuilder このオブジェクト
     */
    public function setPostList(array $postList): self
    {
        $this->postList = array_merge($this->postList, $postList);
        return $this;
    }

    /**
     * @return array
     */
    public function getPostList(): array
    {
        return $this->postList;
    }

    /**
     * @param string $name
     * @param string $value
     * @return RequestBuilder このオブジェクト
     */
    public function setCookie(string $name, string $value): self
    {
        $this->cookieList[$name] = $value;
        return $this;
    }

    /**
     * @param array $cookieList
     * @return RequestBuilder このオブジェクト
     */
    public function setCookieList(array $cookieList): self
    {
        $this->cookieList = array_merge($this->cookieList, $cookieList);
        return $this;
    }

    /**
     * @return array
     */
    public function getCookieList(): array
    {
        return $this->cookieList;
    }

    /**
     * @param string $name
     * @param UploadFile $file
     * @return RequestBuilder このオブジェクト
     */
    public function setUploadFile(string $name, UploadFile $file): self
    {
        $this->fileList[$name] = $file;
        return $this;
    }

    /**
     * @return array
     */
    public function getUploadFileList(): array
    {
        return $this->fileList;
    }

    /**
     * このオブジェクトの設定内容に基づいて Request インスタンスを生成します。
     * method が設定されていない場合は "get" として扱われます。
     * scheme が設定されていない場合は "http" として扱われます。
     * host については明示的に指定する必要があります。設定されていない場合は LogicException をスローします。
     *
     * @return Request
     * @throws LogicException host が設定されていない場合
     */
    public function build(): Request
    {
        return Request::newInstance($this);
    }
}
