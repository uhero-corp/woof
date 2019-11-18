<?php

namespace Woof\Http;

/**
 * HTTP リクエストの添付ファイルをあらわすクラスです。
 */
class UploadFile
{
    /**
     * 添付されたファイルのファイル名です
     *
     * @var string
     */
    private $name;

    /**
     * サーバー上に保管されている添付ファイルのパスです
     *
     * @var string
     */
    private $path;

    /**
     * アップロードの成否をあらわすエラーコードです
     *
     * @var int
     */
    private $errorCode;

    /**
     * ファイルサイズです
     *
     * @var int
     */
    private $size;

    /**
     * @param string $name 添付されたファイルのファイル名
     * @param string $path サーバー上に保管されている添付ファイルのパス
     * @param int $errorCode アップロードの成否をあらわすエラーコード
     * @param int $size 添付ファイルのサイズ
     */
    public function __construct(string $name, string $path, int $errorCode, int $size)
    {
        $this->name      = $name;
        $this->path      = $path;
        $this->errorCode = $errorCode;
        $this->size      = $size;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return file_exists($this->path) ? file_get_contents($this->path) : "";
    }
}
