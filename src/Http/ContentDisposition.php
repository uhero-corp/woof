<?php

namespace Woof\Http;

/**
 * HTTP レスポンスの出力を「名前を付けて保存」させるために使用する HeaderField です。
 */
class ContentDisposition implements HeaderField
{
    /**
     * 保存時に指定されるファイル名です。
     *
     * @var string
     */
    private $filename;

    /**
     * ファイル名を指定して Content-Disposition インスタンスを生成します。
     * 引数を指定しない場合は、保存時のファイル名を指定しない Content-Disposition となります。
     *
     * @param string $filename ファイル名
     */
    public function __construct(string $filename = "")
    {
        $this->filename = $filename;
    }

    /**
     * この Content-Disposition の値を書式化します。
     * ファイル名が指定されている場合は 'attachment; filename="{filename}"' 形式の文字列を返します。
     * ファイル名が存在しない場合は 'attachment' を返します。
     *
     * @return string
     */
    public function format(): string
    {
        if (!strlen($this->filename)) {
            return "attachment";
        }

        $filename = rawurlencode($this->filename);
        return "attachment; filename=\"{$filename}\"";
    }

    /**
     * 文字列 "Content-Disposition" を返します。
     *
     * @return string
     */
    public function getName(): string
    {
        return "Content-Disposition";
    }

    /**
     * ファイル名を返します。
     *
     * @return string
     */
    public function getValue()
    {
        return $this->filename;
    }
}
