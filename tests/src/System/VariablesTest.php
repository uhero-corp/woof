<?php

namespace Woof\System;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Woof\System\Variables
 */
class VariablesTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getServer
     */
    public function testGetServer(): void
    {
        $arr = [
            "HTTP_HOST"   => "localhost",
            "SERVER_NAME" => "localhost",
            "REMOTE_ADDR" => "127.0.0.1",
        ];
        $obj = (new VariablesBuilder())->setServer($arr)->build();
        $this->assertSame($arr, $obj->getServer());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getEnv
     */
    public function testGetEnv(): void
    {
        $arr = [
            "env"  => "prod",
            "test" => "1",
        ];
        $obj = (new VariablesBuilder())->setEnv($arr)->build();
        $this->assertSame($arr, $obj->getEnv());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getGet
     */
    public function testGetGet(): void
    {
        $arr = [
            "process" => "confirm",
            "token"   => "abcd1234",
        ];
        $obj = (new VariablesBuilder())->setGet($arr)->build();
        $this->assertSame($arr, $obj->getGet());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getPost
     */
    public function testGetPost(): void
    {
        $arr = [
            "login"    => "sample",
            "password" => "thisistest",
        ];
        $obj = (new VariablesBuilder())->setPost($arr)->build();
        $this->assertSame($arr, $obj->getPost());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getCookie
     */
    public function testGetCookie(): void
    {
        $arr = [
            "session_id" => "abcd1234",
            "ad_token"   => "9876asdf",
        ];
        $obj = (new VariablesBuilder())->setCookie($arr)->build();
        $this->assertSame($arr, $obj->getCookie());
    }

    /**
     * @covers ::__construct
     * @covers ::newInstance
     * @covers ::getFiles
     */
    public function testGetFiles(): void
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
        $obj = (new VariablesBuilder())->setFiles($arr)->build();
        $this->assertSame($arr, $obj->getFiles());
    }
}
