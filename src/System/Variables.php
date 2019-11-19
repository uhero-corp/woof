<?php

namespace Woof\System;

/**
 * PHP の各種グローバル変数にアクセスするためのクラスです。
 */
class Variables
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

    /**
     * このクラスは VariablesBuilder を使用して構築するため、直接インスタンス化することはできません。
     */
    private function __construct()
    {
        $this->server = [];
        $this->env    = [];
        $this->get    = [];
        $this->post   = [];
        $this->cookie = [];
        $this->files  = [];
    }

    /**
     * このメソッドは VariablesBuilder::build() から参照されます。
     *
     * @param VariablesBuilder $builder
     * @return Variables
     * @ignore
     */
    public static function newInstance(VariablesBuilder $builder): self
    {
        $instance         = new self();
        $instance->server = $builder->getServer();
        $instance->env    = $builder->getEnv();
        $instance->get    = $builder->getGet();
        $instance->post   = $builder->getPost();
        $instance->cookie = $builder->getCookie();
        $instance->files  = $builder->getFiles();
        return $instance;
    }

    /**
     * 現在定義されている各種グローバル定数を参照する Variables インスタンスを返します。
     *
     * @return Variables
     * @codeCoverageIgnore
     */
    public static function getDefaultInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
            $instance->server = $_SERVER;
            $instance->env    = $_ENV;
            $instance->get    = $_GET;
            $instance->post   = $_POST;
            $instance->cookie = $_COOKIE;
            $instance->files  = $_FILES;
        }
        return $instance;
    }

    /**
     * @return array
     */
    public function getServer(): array
    {
        return $this->server;
    }

    /**
     * @return array
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $this->post;
    }

    /**
     * @return array
     */
    public function getGet(): array
    {
        return $this->get;
    }

    /**
     * @return array
     */
    public function getCookie(): array
    {
        return $this->cookie;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
