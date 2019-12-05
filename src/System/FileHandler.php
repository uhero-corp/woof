<?php

namespace Woof\System;

use InvalidArgumentException;

/**
 * 指定されたディレクトリ内のファイルの読み書きを行うためのクラスです。
 */
class FileHandler
{
    /**
     * @var string
     */
    private $dirname;

    /**
     * @param string $dirname ディレクトリ名
     * @throws InvalidArgumentException ディレクトリ名が指定されなかった場合
     * @throws FileSystemException 指定されたディレクトリが存在しない場合
     */
    public function __construct(string $dirname)
    {
        if (!strlen($dirname)) {
            throw new InvalidArgumentException("Directory name required");
        }
        if (!is_dir($dirname)) {
            throw new FileSystemException("Directory not found: '{$dirname}'");
        }

        $this->dirname = rtrim($dirname, "/");
    }

    /**
     * 指定された相対パスを絶対パスに変換します。
     * このメソッドの挙動は、指定されたパスがファイルシステム上に実際に存在するかどうかとは無関係です。
     *
     * @param string $path
     * @return string
     */
    public function formatFullpath(string $path): string
    {
        if (!strlen($path)) {
            throw new InvalidArgumentException("Path required");
        }
        $fixedPath = $this->cleanPath($path);
        if (!strlen($fixedPath)) {
            throw new InvalidArgumentException("Invalid path: '{$path}'");
        }
        return "{$this->dirname}/{$fixedPath}";
    }

    /**
     * @param string $path
     * @return string
     */
    private function cleanPath(string $path): string
    {
        $segments = explode("/", $path);
        $filter   = function (string $str): bool {
            return strlen($str) && ($str !== ".");
        };
        $tmpList  = array_filter($segments, $filter);
        while (true) {
            $index = array_search("..", $tmpList, true);
            if ($index === false) {
                break;
            }
            if ($index === 0) {
                array_shift($tmpList);
            } else {
                array_splice($tmpList, $index -1, 2);
            }
        }
        return implode("/", $tmpList);
    }

    /**
     * @param string $path
     * @return bool
     */
    private function prepareDir(string $path): bool
    {
        $dirname = dirname($path);
        return is_dir($dirname) || mkdir($dirname, 0777, true);
    }

    /**
     * 指定された相対パスに書き込みます。
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function put(string $path, string $contents): bool
    {
        $fullpath = $this->formatFullpath($path);
        $this->prepareDir($fullpath);
        return file_put_contents($fullpath, $contents);
    }

    /**
     * 指定された相対パスに引数の内容を追記します。
     * 改行文字の付与はされないため、行を追加したい場合は手動で改行文字を加える必要があります。
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function append(string $path, string $contents): bool
    {
        $fullpath = $this->formatFullpath($path);
        $this->prepareDir($fullpath);
        return file_put_contents($fullpath, $contents, FILE_APPEND);
    }

    /**
     * 指定されたファイルの中身を取得します。
     * ファイルが存在しない場合は空文字列を返します。
     * このメソッドは、ファイルが存在するかどうかを判定することはできません。
     * ファイルの有無を判定する場合は contains() を使用してください。
     *
     * @param string $path
     * @return string
     */
    public function get(string $path): string
    {
        $fullpath = $this->formatFullpath($path);
        return is_file($fullpath) ? file_get_contents($fullpath) : "";
    }

    /**
     * 指定された相対パスのファイルが存在する場合のみ true を返します。
     *
     * @param string $path
     * @return bool
     */
    public function contains(string $path): bool
    {
        return is_file($this->formatFullpath($path));
    }
}
