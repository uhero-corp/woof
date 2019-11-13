<?php

namespace Woof\Http;

/**
 * HTTP の各種応答ステータスをあらわすクラスです。
 */
class Status
{
    /**
     * "200", "404" など、3 桁の数字から成るコードです。
     *
     * @var string
     */
    private $statusCode;

    /**
     * "OK", "Not Found" など、ステータスの内容をあらわすテキストです。
     *
     * @var string
     */
    private $reasonPhrase;

    /**
     * 指定されたステータスコードおよび文言を持つ Status オブジェクトを生成します。
     *
     * @param string $statusCode
     * @param string $reasonPhrase
     */
    public function __construct(string $statusCode, string $reasonPhrase)
    {
        $this->statusCode   = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * HTTP レスポンスのステータスラインを書式化します。
     *
     * @return string ステータスライン ("HTTP/1.1 OK" など)
     */
    public function format(): string
    {
        return "HTTP/1.1 {$this->statusCode} {$this->reasonPhrase}";
    }

    /**
     * 正常終了をあらわすステータスです。
     *
     * @return Status
     */
    public static function getOK(): self
    {
        return new self("200", "OK");
    }

    /**
     * 指定された URL が移動されたことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get301(): self
    {
        return new self("301", "Moved Permanently");
    }

    /**
     * POST データの処理後などに所定の URL にリダイレクトさせるためのステータスです。
     *
     * @return Status
     */
    public static function get302(): self
    {
        return new self("302", "Found");
    }

    /**
     * 指定された URL について、最後にクライアントに送信してからまだ変更がないことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get304(): self
    {
        return new self("304", "Not Modified");
    }

    /**
     * 受け取った HTTP リクエストが不正なことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get400(): self
    {
        return new self("400", "Bad Request");
    }

    /**
     * 指定された URL について、アクセス権限が必要なことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get401(): self
    {
        return new self("401", "Unauthorized");
    }

    /**
     * 指定された URL へのアクセス権限がないことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get403(): self
    {
        return new self("403", "Forbidden");
    }

    /**
     * 指定された URL が存在しないことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get404(): self
    {
        return new self("404", "File Not Found");
    }

    /**
     * サーバー側で何らかのエラーが発生したことをあらわすステータスです。
     *
     * @return Status
     */
    public static function get500(): self
    {
        return new self("500", "Internal Server Error");
    }
}
