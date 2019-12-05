<?php

namespace Woof\Util;

use InvalidArgumentException;
use Woof\System\FileHandler;
use Woof\Util\IniDecoder;
use Woof\Util\JsonDecoder;
use Woof\Util\StringDecoder;

/**
 * 特定のディレクトリの配下にある INI ファイルまたは JSON ファイルから設定値を取得する Properties の実装です。
 * このクラスは、保管されているファイル名から拡張子を除いたものを第 1 階層のキー名として扱います。
 */
class FileProperties implements Properties
{
    /**
     * @var FileHandler
     */
    private $handler;

    /**
     * キーが文字列, 値が ArrayProperties となる配列です。
     *
     * @var array
     */
    private $data;

    /**
     * ファイルの有無をチェックするための変数です。
     * contains() 内で、該当ファイルが存在しないにも関わらず true を返してしまうのを防ぐために使用します。
     *
     * @var array
     */
    private $files;

    /**
     * @var StringDecoder[]
     */
    private $decList;

    /**
     * @param string $dirname
     * @param array $decoderList
     */
    public function __construct(string $dirname, array $decoderList = [])
    {
        $this->handler = new FileHandler($dirname);
        $this->data    = [];
        $this->files   = [];
        $this->decList = $this->initDecoderList($decoderList);
    }

    /**
     * @return array
     */
    public static function getDefaultStringDecoderList(): array
    {
        return [
            "ini"  => IniDecoder::getInstance(),
            "json" => JsonDecoder::getInstance(),
        ];
    }

    /**
     * @param array $decoderList
     * @return array
     */
    private function initDecoderList(array $decoderList): array
    {
        $callback = function ($value, string $key): bool {
            if (!preg_match("/\\A[a-zA-Z0-9]+\\z/", $key)) {
                return false;
            }
            return ($value instanceof StringDecoder);
        };

        $result = array_filter($decoderList, $callback, ARRAY_FILTER_USE_BOTH);
        return count($result) ? $result : self::getDefaultStringDecoderList();
    }

    /**
     * @param string $basename
     * @return ArrayProperties
     */
    private function getProperties(string $basename): ArrayProperties
    {
        return $this->data[$basename];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool
    {
        list($basename, $sub) = $this->parseSegments($key);
        $this->initBasename($basename);
        if (!$this->files[$basename]) {
            return false;
        }
        return strlen($sub) ? $this->getProperties($basename)->contains($sub) : true;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get(string $key, $defaultValue = null)
    {
        list($basename, $sub) = $this->parseSegments($key);
        $this->initBasename($basename);
        if (!$this->files[$basename]) {
            return $defaultValue;
        }

        $prop = $this->getProperties($basename);
        return strlen($sub) ? $prop->get($sub, $defaultValue) : $prop->getData();
    }

    /**
     * @param string $name
     * @return array
     * @throws InvalidArgumentException
     */
    private function parseSegments(string $name): array
    {
        if (!strlen($name)) {
            throw new InvalidArgumentException("Config key is not specified");
        }

        $matched = [];
        $seg     = "[a-zA-Z0-9_\\-]+";
        if (!preg_match("/\\A({$seg})(\\.{$seg})*\\z/", $name, $matched)) {
            throw new InvalidArgumentException("Invalid config key: '{$name}'");
        }
        $basename = $matched[1];
        $suffix   = substr($name, strlen($basename) + 1);
        return [$basename, $suffix];
    }

    /**
     * @param string $basename
     */
    private function initBasename(string $basename): void
    {
        if (array_key_exists($basename, $this->files)) {
            return;
        }

        $handler = $this->handler;
        $result  = [];
        $exists  = false;
        foreach ($this->decList as $ext => $decoder) {
            $filename = "{$basename}.{$ext}";
            if (!$handler->contains($filename)) {
                continue;
            }
            $arr    = $decoder->parse($handler->get($filename));
            $result = array_merge($result, $arr);
            $exists = true;
        }

        $this->files[$basename] = $exists;
        if ($exists) {
            $this->data[$basename] = new ArrayProperties($result);
        }
    }
}
