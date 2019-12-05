<?php

namespace Woof\Http;

use InvalidArgumentException;
use Woof\Log\Logger;
use Woof\System\Variables;

class RequestLoader
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var HeaderParser
     */
    private $parser;

    /**
     * @param Logger $logger
     * @param HeaderParser $parser
     */
    public function __construct(Logger $logger = null, HeaderParser $parser = null)
    {
        $this->logger = $logger ?? Logger::getNopLogger();
        $this->parser = $parser ?? new HeaderParser();
    }

    /**
     * @return Logger
     */
    public function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return HeaderParser
     */
    public function getHeaderParser(): HeaderParser
    {
        return $this->parser;
    }

    /**
     * @param Variables $var
     * @return Request
     */
    public function load(Variables $var): Request
    {
        $server  = $var->getServer();
        $uri     = $server["REQUEST_URI"] ?? "";
        $builder = (new RequestBuilder())
            ->setHost($server["HTTP_HOST"] ?? "")
            ->setUri($uri)
            ->setPath($this->detectPath($uri))
            ->setScheme(isset($server["HTTPS"]) ? "https" : "http")
            ->setMethod($server["REQUEST_METHOD"] ?? "")
            ->setQueryList($var->getGet())
            ->setPostList($var->getPost())
            ->setCookieList($var->getCookie());
        foreach ($server as $key => $value) {
            if (substr($key, 0, 5) !== "HTTP_") {
                continue;
            }
            $builder->setHeader($this->parseHeader($key, $value));
        }
        foreach ($var->getFiles() as $key => $file) {
            $builder->setUploadFile($key, $this->createUploadFile($file));
        }
        return $builder->build();
    }

    /**
     * @param string $uri
     * @return string
     */
    private function detectPath(string $uri)
    {
        return (false === ($index = strpos($uri, "?"))) ? $uri : substr($uri, 0, $index);
    }

    /**
     * @param string $key
     * @param string $value
     * @return HeaderField
     */
    private function parseHeader(string $key, string $value): HeaderField
    {
        $name = ucwords(strtolower(str_replace("_", "-", substr($key, 5))));
        try {
            return $this->parser->parse($name, $value);
        } catch (InvalidArgumentException $e) {
            $logger = $this->logger;
            $logger->debug("Invalid request header detected: '{$name}'");
            $logger->debug($e->getMessage());
            return EmptyField::getInstance();
        }
    }

    /**
     * @param array $file
     * @return UploadFile
     */
    private function createUploadFile(array $file): UploadFile
    {
        $name      = $file["name"] ?? "";
        $path      = $file["tmp_name"] ?? "";
        $errorCode = $file["error"] ?? "";
        $size      = $file["size"] ?? "";
        return new UploadFile($name, $path, (int) $errorCode, (int) $size);
    }
}
