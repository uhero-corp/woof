<?php

namespace Woof\System;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\System\VariablesBuilder
 */
class VariablesBuilderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::setServer
     * @covers ::getServer
     */
    public function testGetServerAndSetServer(): void
    {
        $arr = [
            "HTTP_HOST"   => "localhost",
            "SERVER_NAME" => "localhost",
            "REMOTE_ADDR" => "127.0.0.1",
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setServer($arr));
        $this->assertSame($arr, $obj->getServer());
    }

    /**
     * @covers ::__construct
     * @covers ::setEnv
     * @covers ::getEnv
     */
    public function testGetEnvAndSetEnv(): void
    {
        $arr = [
            "env"  => "prod",
            "test" => "1",
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setEnv($arr));
        $this->assertSame($arr, $obj->getEnv());
    }

    /**
     * @covers ::__construct
     * @covers ::setGet
     * @covers ::getGet
     */
    public function testGetGetAndSetGet(): void
    {
        $arr = [
            "process" => "confirm",
            "token"   => "abcd1234",
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setGet($arr));
        $this->assertSame($arr, $obj->getGet());
    }

    /**
     * @covers ::__construct
     * @covers ::setPost
     * @covers ::getPost
     */
    public function testGetPostAndSetPost(): void
    {
        $arr = [
            "login"    => "sample",
            "password" => "thisistest",
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setPost($arr));
        $this->assertSame($arr, $obj->getPost());
    }

    /**
     * @covers ::__construct
     * @covers ::setCookie
     * @covers ::getCookie
     */
    public function testGetCookieAndSetCookie(): void
    {
        $arr = [
            "session_id" => "abcd1234",
            "ad_token"   => "9876asdf",
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setCookie($arr));
        $this->assertSame($arr, $obj->getCookie());
    }

    /**
     * @covers ::__construct
     * @covers ::setFiles
     * @covers ::getFiles
     */
    public function testGetFilesAndSetFiles(): void
    {
        $arr = [
            "etc1" => [
                "name"     => "sample01.png",
                "type"     => "image/png",
                "tmp_name" => "/var/tmp/asdf1234",
                "error"    => 0,
                "size"     => 12345,
            ],
            "etc2" => [
                "name"     => "test.log",
                "type"     => "text/plain",
                "tmp_name" => "/var/tmp/abcd9999",
                "error"    => 0,
                "size"     => 5678,
            ],
        ];
        $obj = new VariablesBuilder();
        $this->assertInstanceOf(VariablesBuilder::class, $obj->setFiles($arr));
        $this->assertSame($arr, $obj->getFiles());
    }
}
