<?php

namespace Woof\Http\Response;

interface Body
{
    /**
     * レスポンスボディの本体を返します。
     *
     * @return string
     */
    public function getOutput(): string;

    /**
     * レスポンスボディの本体をクライアントに送信します。
     * 成功時に true を返します。
     *
     * @return bool
     */
    public function sendOutput(): bool;

    /**
     * レスポンスの Content-Type ヘッダを返します。
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * レスポンスの Content-Length ヘッダの値を返します。
     *
     * @return int
     */
    public function getContentLength(): int;
}
