<?php

namespace Woof\Web\Session;

use DirectoryIterator;
use Woof\Log\Logger;
use Woof\System\Clock;
use Woof\System\DefaultClock;
use Woof\System\FileSystemException;

class FileSessionContainer implements SessionContainer
{
    /**
     * @var string
     */
    private $dirname;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @param string $dirname
     * @param Logger $logger
     * @param Clock $clock
     */
    public function __construct(string $dirname, Logger $logger = null, Clock $clock = null)
    {
        if (!is_dir($dirname)) {
            throw new FileSystemException("Directory not found: {$dirname}");
        }

        $this->dirname = $dirname;
        $this->logger  = $logger ?? Logger::getNopLogger();
        $this->clock   = $clock ?? DefaultClock::getInstance();
    }

    /**
     * @param int $maxAge
     * @return int
     */
    public function cleanExpiredSessions(int $maxAge): int
    {
        $dir   = new DirectoryIterator($this->dirname);
        $now   = $this->clock->getTime();
        $count = 0;
        foreach ($dir as $entry) {
            $filename = $entry->getFilename();
            if (substr($filename, 0, 5) !== "sess_") {
                continue;
            }

            $path  = "{$this->dirname}/{$filename}";
            $limit = filemtime($path) + $maxAge;
            if ($limit < $now) {
                @unlink($path) && $count++;
                $this->logger->debug("Session removed: '{$filename}'");
            }
        }
        return $count;
    }

    /**
     * @param string $id
     * @param int $maxAge
     * @return bool
     */
    public function contains(string $id, int $maxAge): bool
    {
        $filename = $this->formatFilename($id);
        if (!is_file($filename)) {
            return false;
        }
        return $this->clock->getTime() < (filemtime($filename) + $maxAge);
    }

    /**
     * @param string $id
     * @return string
     */
    private function formatFilename(string $id): string
    {
        return "{$this->dirname}/sess_{$id}";
    }

    /**
     * @param string $id
     * @return array
     */
    public function load(string $id): array
    {
        $filename = $this->formatFilename($id);
        if (!is_file($filename)) {
            return [];
        }
        try {
            touch($filename, $this->clock->getTime());
            $serialized = trim(file_get_contents($filename));
            $parser     = new ParserContext($serialized);
            return $parser->parse();
        } catch (ParseException $e) {
            $logger = $this->logger;
            $logger->alert("Failed to parse session for ID '{$id}'");
            $logger->alert($e->getMessage());
            return [];
        }
    }

    /**
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function save(string $id, array $data): bool
    {
        $filename   = $this->formatFilename($id);
        $serialized = $this->serialize($data);
        $result     = file_put_contents($filename, $serialized) && chmod($filename, 0666);
        if (!$result) {
            $this->logger->alert("Failed to save session to '{$filename}'");
        }
        return $result;
    }

    /**
     * @param array $data
     * @return string
     */
    private function serialize(array $data): string
    {
        $result = "";
        foreach ($data as $key => $value) {
            $serialized = serialize($value);
            $result     .= "{$key}|{$serialized}";
        }
        return $result;
    }
}
