<?php

require_once(dirname(__DIR__) . "/vendor/autoload.php");

define("TEST_DATA_DIR", __DIR__ . "/data");

class TestHelper
{
    /**
     * このクラスはインスタンス化しません。
     */
    private function __construct()
    {

    }

    /**
     * 指定されたディレクトリ内の全ファイルのうち、
     * .gitignore や .gitkeep など "." から始まるファイル名以外のものをすべて削除します。
     * もしもサブディレクトリが存在していた場合は再帰的に処理します。
     * 処理の結果ディレクトリが空になった場合はディレクトリ自体も削除します。
     *
     * @param string $dirname ディレクトリ名
     * @return bool ディレクトリ自体が削除された場合は true
     */
    public static function cleanDirectory(string $dirname): bool
    {
        $di     = new DirectoryIterator($dirname);
        $result = true;
        foreach ($di as $i) {
            if ($i->isDot()) {
                continue;
            }
            $filename = $i->getFilename();
            if (substr($filename, 0, 1) === ".") {
                $result = false;
                continue;
            }

            $fullpath = "{$dirname}/{$filename}";
            $next     = is_dir($fullpath) ? self::cleanDirectory($fullpath) : unlink($fullpath);
            $result   = $next && $result;
        }
        if ($result) {
            rmdir($dirname);
        }
        return $result;
    }
}
