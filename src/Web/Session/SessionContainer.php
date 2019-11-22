<?php

namespace Woof\Web\Session;

interface SessionContainer
{
    /**
     * 指定された ID のセッションが存在するかどうかを判定します。
     * 引数の ID が存在し、かつ有効期限内の場合のみ true を返します。
     *
     * @param string $id セッション ID
     * @param int $maxAge セッションの生存期間 (秒数)
     * @return bool セッションが存在する場合のみ true
     */
    public function contains(string $id, int $maxAge): bool;

    /**
     * 指定された ID のセッションを取り出します。
     * セッションが存在しない場合は空の配列を返します。
     *
     * @param string $id セッション ID
     * @return array セッション一覧。存在しない場合は空の配列
     */
    public function load(string $id): array;

    /**
     * 指定されたセッションを保存します。
     *
     * @param string $id
     * @param array 保存するセッションの一覧
     * @return bool 書き込みに成功した場合に true
     */
    public function save(string $id, array $data): bool;

    /**
     * 有効期限切れのセッションを削除します。
     * 削除された件数を返します。
     *
     * @param int $maxAge セッションの生存期間 (秒数)
     * @return int 削除されたセッションの件数
     */
    public function cleanExpiredSessions(int $maxAge): int;
}
