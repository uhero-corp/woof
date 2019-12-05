<?php

namespace Woof\Http\Response;

/**
 * 指定された文字列を直接レスポンスボディとして送信する、シンプルな Body の実装です。
 */
class TextBody implements Body
{
    /**
     * @var string
     */
    private $output;

    /**
     * @var string
     */
    private $contentType;

    /**
     * 指定されたレスポンスボディを持つ新しい TextBody を生成します。
     *
     * @param string $output 送信するレスポンスボディ
     * @param string $contentType Content-Type の値。省略した場合は "text/html; charset=UTF-8"
     */
    public function __construct($output, $contentType = "text/html; charset=UTF-8")
    {
        $this->output      = $output;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return bool
     */
    public function sendOutput(): bool
    {
        echo $this->output;
        return true;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getContentLength(): int
    {
        return strlen($this->output);
    }
}
