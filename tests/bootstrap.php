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

    /**
     * 指定されたディレクトリのファイルすべてを、もう一方のディレクトリにコピーします。
     * サブディレクトリが存在していた場合は再帰的にコピーされます。
     * ただし .gitignore や .gitkeep など "." から始まるファイル名のものは除外されます。
     *
     * @param string $sourcedir
     * @param string $targetdir
     * @return bool
     */
    public static function copyDirectory(string $sourcedir, string $targetdir): bool
    {
        $result = self::mkdirAll($targetdir);
        $di     = new DirectoryIterator($sourcedir);
        foreach ($di as $i) {
            // "." や ".." などのディレクトリと, その他 '.' から始まるファイル名を除外します
            $filename = $i->getFilename();
            if (substr($filename, 0, 1) === ".") {
                continue;
            }
            $sourcePath = "{$sourcedir}/{$filename}";
            $targetPath = "{$targetdir}/{$filename}";
            $result     = $result && ($i->isDir() ? self::copyDirectory($sourcePath, $targetPath) : copy($sourcePath, $targetPath));
        }
        return $result;
    }

    /**
     * @param string $dirname
     * @return bool
     */
    private static function mkdirAll(string $dirname): bool
    {
        return is_dir($dirname) || (self::mkdirAll(dirname($dirname)) && mkdir($dirname) && chmod($dirname, 0777));
    }
}
