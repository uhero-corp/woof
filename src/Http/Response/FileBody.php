<?php

namespace Woof\Http\Response;

use Woof\System\FileSystemException;

/**
 * 指定されたファイルのデータをそのままレスポンスボディとするクラスです。
 */
class FileBody implements Body
{
    /**
     * @var string
     */
    private $filename;

    /**
     *
     * @var string
     */
    private $contentType;

    /**
     * @param string $filename
     * @param string $contentType
     * @throws FileSystemException 対象のファイルが存在しないか読み込めない場合
     */
    public function __construct(string $filename, string $contentType)
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new FileSystemException("File not found: {$filename}");
        }
        $this->filename    = $filename;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return file_get_contents($this->filename);
    }

    /**
     * @return bool
     */
    public function sendOutput(): bool
    {
        return (readfile($this->filename) !== false);
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
        return filesize($this->filename);
    }
}
