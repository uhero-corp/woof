<?php

namespace Woof\Web;

use Woof\Http\ContentDisposition;
use Woof\Http\HeaderField;
use Woof\Http\Request;
use Woof\Http\Response;
use Woof\Http\Response\Body;
use Woof\Http\Response\CookieAttributes;
use Woof\Http\Response\CookieAttributesBuilder;
use Woof\Http\ResponseBuilder;
use Woof\Http\Status;
use Woof\Http\TextField;

class Operator
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var WebEnvironment
     */
    private $env;

    /**
     * @var ResponseBuilder
     */
    private $builder;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Request $request
     * @param WebEnvironment $env
     * @param Response $response
     */
    public function __construct(Request $request, WebEnvironment $env, Response $response = null)
    {
        $this->request = $request;
        $this->env     = $env;
        $this->builder = new ResponseBuilder($response);
    }

    /**
     * @return ResponseBuilder
     */
    public function getResponseBuilder(): ResponseBuilder
    {
        return $this->builder;
    }

    /**
     * @return Session
     */
    public function getSessionObject(): Session
    {
        if ($this->session === null) {
            $this->session = $this->env->getSessionStorage()->getSession($this->request);
        }
        return $this->session;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Operator このオブジェクト
     */
    public function setSession(string $key, $value): self
    {
        $this->getSessionObject()->set($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getSession(string $key, $defaultValue = null)
    {
        return $this->getSessionObject()->get($key, $defaultValue);
    }

    /**
     * @return Operator このオブジェクト
     */
    public function saveSession(): self
    {
        $session = $this->getSessionObject();
        if ($session->isNew() && !$session->isChanged()) {
            return $this;
        }

        $ss = $this->env->getSessionStorage();
        $ss->save($session);
        if ($session->isNew()) {
            $attr = (new CookieAttributesBuilder())
                ->setPath($this->env->getContext()->getRootPath())
                ->build();
            $this->builder->setCookie($ss->getKey(), $session->getId(), $attr);
        }
        return $this;
    }

    /**
     * @param HeaderField $header
     * @return Operator このオブジェクト
     */
    public function setHeader(HeaderField $header): self
    {
        $this->builder->setHeader($header);
        return $this;
    }

    /**
     * HTTP レスポンスの本文を出力するための View を指定します。
     *
     * @param View $view
     * @return Operator このオブジェクト
     */
    public function setView(View $view): self
    {
        $this->builder->setBody(new ViewBody($view, $this->env->getResources(), $this->env->getContext()));
        return $this;
    }

    /**
     * @param Body $body
     * @return Operator このオブジェクト
     */
    public function setBody(Body $body): self
    {
        $this->builder->setBody($body);
        return $this;
    }

    /**
     * @param Status $status
     * @return Operator このオブジェクト
     */
    public function setStatus(Status $status): self
    {
        $this->builder->setStatus($status);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @param CookieAttributes $attr
     * @return Operator このオブジェクト
     */
    public function setCookie(string $name, string $value, CookieAttributes $attr = null): self
    {
        $this->builder->setCookie($name, $value, $attr);
        return $this;
    }

    /**
     * @param string $path
     * @param array $queryList
     * @return string
     */
    public function formatAbsoluteUrl(string $path, array $queryList): string
    {
        $href = $this->env->getContext()->formatHref($path, $queryList);
        if (preg_match("/\\Ahttps?:\\/\\//", $href)) {
            return $href;
        }

        $request = $this->request;
        $scheme  = $request->getScheme();
        if (preg_match("/\\A\\/\\//", $href)) {
            return "{$scheme}:{$href}";
        }

        $host = $request->getHost();
        return "{$scheme}://{$host}{$href}";
    }

    /**
     * @param string $appPath
     * @param array $queryList
     * @return Operator このオブジェクト
     */
    public function setRedirect(string $appPath, array $queryList = []): self
    {
        $url     = $this->formatAbsoluteUrl($appPath, $queryList);
        $builder = $this->builder;
        if (!$builder->hasStatus()) {
            $builder->setStatus(Status::get302());
        }
        $builder->setHeader(new TextField("Location", $url));
        return $this;
    }

    /**
     * @param int $mtime
     * @param string $etag
     * @return bool
     */
    public function checkNotModified(int $mtime, string $etag): bool
    {
        $request = $this->request;
        $ifm     = $request->getHeader("If-Modified-Since");
        $ifn     = $request->getHeader("If-None-Match");
        return ($ifm->getValue() === $mtime && $ifn->getValue() === $etag);
    }

    /**
     * HTTP レスポンスの本文データをブラウザに「名前を付けて保存」させるためのファイル名を指定します。
     * このメソッドが実行された場合 HTTP レスポンスに Content-Disposition ヘッダーが付与され、
     * 引数に指定されたファイル名が filename として設定されます。
     *
     * 引数なしでこのメソッドを実行することで、デフォルトの保存ファイル名を未指定にすることができます。
     *
     * @param string $filename
     * @return Operator このオブジェクト
     */
    public function setAttachmentFilename(string $filename = ""): self
    {
        return $this->setHeader(new ContentDisposition($filename));
    }

    /**
     * @return Response
     */
    public function build(): Response
    {
        return $this->builder->build();
    }
}
