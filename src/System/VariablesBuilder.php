<?php

namespace Woof\System;

class VariablesBuilder
{
    /**
     * グローバル変数 $_SERVER に相当する配列です。
     *
     * @var array
     */
    private $server;

    /**
     * グローバル変数 $_ENV に相当する配列です。
     *
     * @var array
     */
    private $env;

    /**
     * グローバル変数 $_GET に相当する配列です。
     *
     * @var array
     */
    private $get;

    /**
     * グローバル変数 $_POST に相当する配列です。
     *
     * @var array
     */
    private $post;

    /**
     * グローバル変数 $_COOKIE に相当する配列です。
     *
     * @var array
     */
    private $cookie;

    /**
     * グローバル変数 $_FILES に相当する配列です。
     *
     * @var array
     */
    private $files;

    public function __construct()
    {
        $this->server = [];
        $this->env    = [];
        $this->get    = [];
        $this->post   = [];
        $this->cookie = [];
        $this->files  = [];
    }

    /**
     * @param array $server
     * @return VariablesBuilder
     */
    public function setServer(array $server): self
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return array
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @param array $env
     * @return VariablesBuilder
     */
    public function setEnv(array $env): self
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return array
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * @param array $get
     * @return VariablesBuilder
     */
    public function setGet(array $get): self
    {
        $this->get = $get;
        return $this;
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @param array $post
     * @return VariablesBuilder
     */
    public function setPost(array $post): self
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @param array $cookie
     * @return VariablesBuilder
     */
    public function setCookie(array $cookie): self
    {
        $this->cookie = $cookie;
        return $this;
    }

    /**
     * @return array
     */
    public function getCookie(): array
    {
        return $this->cookie;
    }

    /**
     * @param array $files
     * @return VariablesBuilder
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return Variables
     */
    public function build(): Variables
    {
        return Variables::newInstance($this);
    }
}
