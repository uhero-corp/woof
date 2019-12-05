<?php

namespace Woof\Web;

use InvalidArgumentException;

class Session
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $isNew;

    /**
     * @var bool
     */
    private $isChanged;

    /**
     * @param string $id
     * @param array $data
     * @param boolean $isNew
     */
    public function __construct(string $id, array $data, bool $isNew = false)
    {
        if (!self::validateId($id)) {
            throw new InvalidArgumentException("Invalid session ID: '{$id}'");
        }
        $this->id        = $id;
        $this->data      = $data;
        $this->isNew     = $isNew;
        $this->isChanged = false;
    }

    /**
     * @param string $id
     * @return bool
     */
    public static function validateId($id): bool
    {
        return 0 < preg_match("/\\A[0-9a-zA-Z,\\-]+\\z/", $id);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->isChanged  = true;
    }

    /**
     * @param string $key
     * @param mixed  $defaultValue
     */
    public function get($key, $defaultValue = null)
    {
        return $this->data[$key] ?? $defaultValue;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->isNew;
    }

    /**
     * @return bool
     */
    public function isChanged(): bool
    {
        return $this->isChanged;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !count($this->data);
    }
}
