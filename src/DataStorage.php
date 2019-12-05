<?php

namespace Woof;

/**
 * アプリケーション内の動的なデータの読み書きを行うためのインタフェースです。
 */
interface DataStorage
{
    /**
     * 指定されたキーに相当するデータを返します。
     * 引数のキーが存在しない場合は $defaultValue を返します。
     *
     * @param string $key
     * @param string $defaultValue 指定されたキーが見つからなかった場合に使用される値
     * @return string
     */
    public function get(string $key, string $defaultValue = ""): string;

    /**
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool;

    /**
     * @param string $key
     * @param string $contents
     * @return bool
     */
    public function put(string $key, string $contents): bool;

    /**
     * 指定されたキーに相当するデータの末尾に追記します。
     *
     * @param string $key
     * @param string $contents
     * @return bool
     */
    public function append(string $key, string $contents): bool;
}
