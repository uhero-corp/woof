<?php

namespace Woof\Http;

use LogicException;

class Request
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

    /**
     * このクラスは RequestBuilder を使用して初期化します。
     */
    private function __construct()
    {
        $this->headerList = [];
        $this->queryList  = [];
        $this->postList   = [];
        $this->cookieList = [];
        $this->fileList   = [];
    }

    /**
     * このメソッドは RequestBuilder::build() から参照されます。
     *
     * @param RequestBuilder $builder
     * @return Request
     * @throws LogicException
     * @ignore
     */
    public static function newInstance(RequestBuilder $builder): self
    {
        if (!strlen($host = $builder->getHost())) {
            throw new LogicException("Host is not specified");
        }
        $scheme = strtolower($builder->getScheme());
        $method = strtolower($builder->getMethod());

        $req             = new self();
        $req->host       = $host;
        $req->scheme     = strlen($scheme) ? $scheme : "http";
        $req->method     = strlen($method) ? $method : "get";
        $req->uri        = $builder->getUri();
        $req->path       = $builder->getPath();
        $req->headerList = $builder->getHeaderList();
        $req->queryList  = $builder->getQueryList();
        $req->postList   = $builder->getPostList();
        $req->cookieList = $builder->getCookieList();
        $req->fileList   = $builder->getUploadFileList();
        return $req;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * アクセスされた URL そのものを返します。返り値はクエリ以降の文字列を含みます。
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * アクセスされた URL のクエリを含まない部分を返します。
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
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
     * @param string $name
     * @param string|array $defaultValue
     * @return string|array
     */
    public function getQuery(string $name, $defaultValue = null)
    {
        return $this->queryList[$name] ?? $defaultValue;
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
     * @param string|array $defaultValue
     * @return string|array
     */
    public function getPost(string $name, $defaultValue = null)
    {
        return $this->postList[$name] ?? $defaultValue;
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
     * @param string $defaultValue
     * @return string
     */
    public function getCookie(string $name, string $defaultValue = null)
    {
        return $this->cookieList[$name] ?? $defaultValue;
    }

    /**
     * @return array
     */
    public function getCookieList(): array
    {
        return $this->cookieList;
    }

    /**
     * 指定されたパラメータ名の添付ファイルが存在するかどうか調べます。
     *
     * @param string $name
     * @return bool
     */
    public function hasUploadFile(string $name): bool
    {
        return array_key_exists($name, $this->fileList);
    }

    /**
     * 指定されたパラメータ名の添付ファイルを取得します。
     *
     * @param string $name
     * @return UploadFile
     * @throws UploadFileNotFoundException 添付ファイルが存在しない場合
     */
    public function getUploadFile($name): UploadFile
    {
        if (!$this->hasUploadFile($name)) {
            throw new UploadFileNotFoundException("File not uploaded: {$name}");
        }

        return $this->fileList[$name];
    }

    /**
     * @return UploadFile[]
     */
    public function getUploadFileList(): array
    {
        return $this->fileList;
    }
}
