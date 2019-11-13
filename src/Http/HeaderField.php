<?php

namespace Woof\Http;

/**
 * HTTP リクエストおよびレスポンスにおける各種ヘッダーフィールドをあらわします。
 */
interface HeaderField
{
    /**
     * このヘッダーフィールドのヘッダー名を返します.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * このヘッダーフィールドの値を返します。
     *
     * @return mixed
     */
    public function getValue();

    /**
     * このヘッダーフィールドの値を文字列に変換します。
     *
     * @return string
     */
    public function format(): string;
}
