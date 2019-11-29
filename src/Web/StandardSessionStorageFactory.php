<?php

namespace Woof\Web;

use Woof\Config;
use Woof\DataStorage;
use Woof\FileDataStorage;
use Woof\Log\Logger;
use Woof\Web\Session\FileSessionContainer;
use Woof\Web\Session\SessionStorage;
use Woof\Web\Session\SessionStorageBuilder;

class StandardSessionStorageFactory
{
    /**
     * @param Config $config
     * @param DataStorage $data
     * @return SessionStorage
     */
    public function create(Config $config, DataStorage $data = null, Logger $logger = null): SessionStorage
    {
        $sub      = $config->getSubConfig("session");
        $savePath = $this->getSessionSavePath($sub, $data);
        is_dir($savePath) || mkdir($savePath, 0777, true);

        return (new SessionStorageBuilder())
            ->setSessionContainer(new FileSessionContainer($savePath, $logger))
            ->setKey($this->getSessionKey($sub))
            ->setMaxAge($this->getMaxAge($sub))
            ->setGcProbability($this->getGcProbability($sub))
            ->build();
    }

    /**
     * @param Config $sub
     * @param DataStorage $data
     * @return string
     */
    private function getSessionSavePath(Config $sub, DataStorage $data = null)
    {
        if ($data instanceof FileDataStorage) {
            $dirname = $sub->getString("dirname", "sessions");
            return $data->formatPath($dirname);
        } else {
            $savePath = session_save_path();
            return strlen($savePath) ? $savePath : sys_get_temp_dir();
        }
    }

    /**
     * @param Config $sub
     * @return string
     */
    private function getSessionKey(Config $sub): string
    {
        $def  = session_name();
        $name = $sub->getString("keyname", $def);
        return strlen($name) ? $name : $def;
    }

    /**
     * @param Config $sub
     * @return int
     */
    private function getMaxAge(Config $sub): int
    {
        return $sub->getInt("max-age", ini_get("session.gc_maxlifetime"), 60, 7200);
    }

    /**
     * @param Config $sub
     * @return float
     */
    private function getGcProbability(Config $sub): float
    {
        $p   = ini_get("session.gc_probability");
        $d   = ini_get("session.gc_divisor");
        $def = (0 < $p && 0 < $d) ? (float) ($p / $d) : 0.0;
        return $sub->getFloat("gc-probability", $def, 0.0, 1.0);
    }
}
