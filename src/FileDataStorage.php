<?php

namespace Woof;

use InvalidArgumentException;
use Woof\System\FileHandler;
use Woof\System\FileSystemException;

class FileDataStorage implements DataStorage
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
     * @param $defaultValue
     * @return string
     */
    public function get(string $key, string $defaultValue = ""): string
    {
        return $this->handler->contains($key) ? $this->handler->get($key) : $defaultValue;
    }

    /**
     *
     * @param string $path
     * @return bool
     */
    public function contains(string $path): bool
    {
        return $this->handler->contains($path);
    }

    /**
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function put(string $path, string $contents): bool
    {
        return $this->handler->put($path, $contents);
    }

    /**
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function append(string $path, string $contents): bool
    {
        return $this->handler->append($path, $contents);
    }

    /**
     * @param string $path
     * @return string
     */
    public function formatPath(string $path): string
    {
        return $this->handler->formatFullpath($path);
    }
}
