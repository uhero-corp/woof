<?php

namespace Woof;

/**
 * アプリケーションの実行に必要な、プログラム以外の各種リソースを取り出すためのインタフェースです。
 * 主要なリソースの例として HTML テンプレート, システムメッセージの翻訳情報, 各種メディアファイルなどが挙げられます。
 */
interface Resources
{
    /**
     * 指定されたキーに該当するリソースを取得し、文字列として返します。
     *
     * @param string $key
     * @return string
     * @throws ResourceNotFoundException 指定されたリソースが存在しない場合
     */
    public function get(string $key): string;

    /**
     * 指定されたキーに相当するリソースが存在するかどうかを判定します。
     *
     * @param string $key
     * @return bool
     */
    public function contains(string $key): bool;
}
