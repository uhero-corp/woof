<?php

namespace Woof;

use InvalidArgumentException;
use Woof\System\FileHandler;
use Woof\System\FileSystemException;

/**
 * 特定のディレクトリに格納されている各種リソースファイルを取得するための Resources です。
 * このクラスは指定されたディレクトリからの相対パスをキーとして扱います。
 */
class FileResources implements Resources
{
    /**
     * @var FileHandler
     */
    private $handler;

    /**
     * @param string $dirname
     * @throws InvalidArgumentException
     * @throws FileSystemException
     */
    public function __construct(string $dirname)
    {
        $this->handler = new FileHandler($dirname);
    }

    /**
     * @param string $key
     * @return string
     * @extends ResourceNotFoundException
     */
    public function get(string $key): string
    {
        if (!$this->handler->contains($key)) {
            throw new ResourceNotFoundException("Resource not found: '{$key}'");
        }
        return $this->handler->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool
    {
        return $this->handler->contains($key);
    }

    /**
     * 指定されたキーを、ファイルシステム上の絶対パスに変換します。
     *
     * @param string $key
     * @return string
     */
    public function formatPath(string $key): string
    {
        return $this->handler->formatFullpath($key);
    }
}
